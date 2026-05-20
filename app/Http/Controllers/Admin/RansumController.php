<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\RansumImport;
use App\Imports\RansumParser;
use App\Models\Product;
use App\Models\RansumItem;
use App\Models\RansumUpload;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class RansumController extends Controller
{
    private const PRICE_DECIMAL_HEURISTIC_MAX_SCALE = 2;

    protected string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = storage_path('app/private/ransum_uploads');
        File::ensureDirectoryExists($this->uploadDir, 0755);
    }

    // ------------------------------------------------------------------
    // Index – list all uploads
    // ------------------------------------------------------------------

    public function index()
    {
        $uploads = RansumUpload::with('uploader')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.ransum.index', compact('uploads'));
    }

    // ------------------------------------------------------------------
    // Upload – store file, check duplicate & corruption
    // ------------------------------------------------------------------

    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        $file = $request->file('excel_file');

        // --- Duplicate-upload check (content hash) ---
        $hash = hash_file('sha256', $file->getRealPath());

        if (RansumUpload::where('file_hash', $hash)->exists()) {
            return back()->withErrors([
                'excel_file' => __('File ini sudah pernah diupload sebelumnya. Silakan periksa daftar upload.'),
            ])->withInput();
        }

        // --- Corruption / structure check ---
        try {
            $import = new RansumImport();
            Excel::import($import, $file);
            $rawData = $import->getData();
        } catch (\Throwable $e) {
            return back()->withErrors([
                'excel_file' => __('File tidak dapat dibaca atau rusak. Silakan upload ulang file yang valid.'),
            ])->withInput();
        }

        if (empty($rawData)) {
            return back()->withErrors([
                'excel_file' => __('File Excel kosong atau tidak memiliki data yang dapat dibaca.'),
            ])->withInput();
        }

        // --- Parse header to validate template ---
        $parser = new RansumParser($rawData);
        $header = $parser->parseHeader();

        // Minimal template validation: vessel code or vessel name must be present
        if (empty($header['vessel_code']) && empty($header['vessel_name'])) {
            return back()->withErrors([
                'excel_file' => __('Template tidak valid. Pastikan file menggunakan format BPB Ransum yang benar.'),
            ])->withInput();
        }

        // --- Store file ---
        $extension  = strtolower($file->getClientOriginalExtension());
        $baseName   = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName   = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName);
        $storedName = uniqid('ransum_', true) . '_' . $safeName . '.' . $extension;

        $file->move($this->uploadDir, $storedName);

        // --- Persist header record ---
        $upload = RansumUpload::create(array_merge($header, [
            'file_hash'         => $hash,
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename'   => $storedName,
            'status'            => 'pending',
            'uploaded_by'       => auth()->id(),
        ] + $parser->parseSignatures()));

        return redirect()->route('admin.ransum.preview', $upload->id)
            ->with('success', __('File berhasil diupload. Silakan periksa preview sebelum import.'));
    }

    // ------------------------------------------------------------------
    // Preview – show extracted header + items before import
    // ------------------------------------------------------------------

    public function preview(int $id)
    {
        $upload = RansumUpload::findOrFail($id);

        // For both pending and imported: read from DB (seeding first if needed).
        if (in_array($upload->status, ['pending', 'imported'])) {
            // Seed draft items from Excel on first visit (only when no DB rows exist yet).
            if ($upload->status === 'pending' && ! RansumItem::where('ransum_upload_id', $id)->exists()) {
                $filePath = $this->uploadDir . '/' . $upload->stored_filename;

                if (! file_exists($filePath)) {
                    return redirect()->route('admin.ransum.index')
                        ->with('error', __('File tidak ditemukan.'));
                }

                try {
                    $importObj = new RansumImport();
                    Excel::import($importObj, $filePath);
                    $parser    = new RansumParser($importObj->getData());
                    $flatItems = $parser->parseItemsFlat();
                } catch (\Throwable $e) {
                    return redirect()->route('admin.ransum.index')
                        ->with('error', __('Gagal membaca file: ') . $e->getMessage());
                }

                DB::transaction(function () use ($upload, $flatItems) {
                    foreach ($flatItems as $item) {
                        RansumItem::create(array_merge(['ransum_upload_id' => $upload->id], $item));
                    }
                });
            }

            $dbItems = RansumItem::where('ransum_upload_id', $id)->orderBy('id')->get();
            $grouped = [];
            foreach ($dbItems as $item) {
                $sec = $item->section ?? 'UNKNOWN';
                $grouped[$sec]['section'] = $sec;
                $grouped[$sec]['items'][] = $item->toArray();
            }
            $sections   = array_values($grouped);
            $isEditable = true;
            return view('admin.ransum.preview', compact('upload', 'sections', 'isEditable'));
        }

        // Fallback for any unexpected status: parse from file, read-only.
        $filePath = $this->uploadDir . '/' . $upload->stored_filename;

        if (! file_exists($filePath)) {
            return redirect()->route('admin.ransum.index')
                ->with('error', __('File tidak ditemukan.'));
        }

        try {
            $importObj = new RansumImport();
            Excel::import($importObj, $filePath);
            $parser   = new RansumParser($importObj->getData());
            $sections = $parser->parseItems();
        } catch (\Throwable $e) {
            return redirect()->route('admin.ransum.index')
                ->with('error', __('Gagal membaca file: ') . $e->getMessage());
        }

        $isEditable = false;
        return view('admin.ransum.preview', compact('upload', 'sections', 'isEditable'));
    }

    // ------------------------------------------------------------------
    // Import – persist items to DB
    // ------------------------------------------------------------------

    public function import(int $id)
    {
        $upload = RansumUpload::findOrFail($id);

        if ($upload->status === 'imported') {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('Data dari file ini sudah pernah diimport.'));
        }

        $filePath = $this->uploadDir . '/' . $upload->stored_filename;

        if (! file_exists($filePath)) {
            return redirect()->route('admin.ransum.index')
                ->with('error', __('File tidak ditemukan.'));
        }

        try {
            $import = new RansumImport();
            Excel::import($import, $filePath);
            $parser = new RansumParser($import->getData());
            $items  = $parser->parseItemsFlat();
        } catch (\Throwable $e) {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('Gagal membaca file: ') . $e->getMessage());
        }

        DB::transaction(function () use ($upload, $items) {
            foreach ($items as $item) {
                RansumItem::create(array_merge(['ransum_upload_id' => $upload->id], $item));
            }

            $upload->update([
                'status'      => 'imported',
                'imported_at' => now(),
            ]);
        });

        return redirect()->route('admin.ransum.preview', $upload->id)
            ->with('success', __('Berhasil mengimport :count item ke database.', ['count' => count($items)]));
    }

    // ------------------------------------------------------------------
    // Serve Signature Photo (private storage)
    // ------------------------------------------------------------------

    public function servePhoto(int $id, string $type)
    {
        if (!in_array($type, ['pemohon', 'menyetujui'])) {
            abort(404);
        }

        $upload = RansumUpload::findOrFail($id);
        $photoPath = $upload->{$type . '_photo'};

        if (!$photoPath) {
            abort(404);
        }

        $fullPath = $this->uploadDir . '/' . $photoPath;

        if (!file_exists($fullPath)) {
            abort(404);
        }

        $mime = mime_content_type($fullPath) ?: 'image/jpeg';
        return response()->file($fullPath, ['Content-Type' => $mime]);
    }

    // ------------------------------------------------------------------
    // Upload Signature Photo
    // ------------------------------------------------------------------

    public function uploadPhoto(Request $request, int $id, string $type)
    {
        if (!in_array($type, ['pemohon', 'menyetujui'])) {
            abort(404);
        }

        $request->validate([
            'photo' => ['required', 'file', 'image', 'max:5120'],
        ]);

        $upload = RansumUpload::findOrFail($id);

        // Build subfolder named after original Excel file (without extension)
        $baseName  = pathinfo($upload->original_filename, PATHINFO_FILENAME);
        $safeBase  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName);
        $subDir    = $this->uploadDir . '/' . $safeBase;
        File::ensureDirectoryExists($subDir, 0755);

        // Name the image after the signer
        $signerName = $type === 'pemohon' ? $upload->pemohon : $upload->menyetujui;
        $safeSigner = $signerName
            ? preg_replace('/[^A-Za-z0-9_\-]/', '_', $signerName)
            : $type;

        $ext      = strtolower($request->file('photo')->getClientOriginalExtension()) ?: 'jpg';
        $filename = $safeSigner . '.' . $ext;

        // Remove old photo if different filename
        $oldPhoto = $upload->{$type . '_photo'};
        if ($oldPhoto && $oldPhoto !== ($safeBase . '/' . $filename)) {
            $oldPath = $this->uploadDir . '/' . $oldPhoto;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $request->file('photo')->move($subDir, $filename);

        $upload->update([$type . '_photo' => $safeBase . '/' . $filename]);

        return redirect()->route('admin.ransum.preview', $upload->id)
            ->with('success', __('Foto :type berhasil diupload.', ['type' => ucfirst($type)]));
    }

    // ------------------------------------------------------------------
    // Item CRUD (available for pending and imported status)
    // ------------------------------------------------------------------

    public function storeItem(Request $request, int $id)
    {
        $upload = RansumUpload::findOrFail($id);

        if (! in_array($upload->status, ['pending', 'imported'])) {
            return redirect()->route('admin.ransum.preview', $id)
                ->with('error', __('Tidak dapat menambahkan item pada status ini.'));
        }

        $validated = $request->validate($this->itemValidationRules());

        RansumItem::create(array_merge(['ransum_upload_id' => $id], $validated));

        return redirect()->route('admin.ransum.preview', $id)
            ->with('success', __('Item berhasil ditambahkan.'));
    }

    // ------------------------------------------------------------------
    // Finalize – mark upload as imported (draft items already in DB)
    // ------------------------------------------------------------------

    public function finalize(int $id)
    {
        $upload = RansumUpload::findOrFail($id);

        if ($upload->status !== 'pending') {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('Hanya upload dengan status pending yang dapat difinalisasi.'));
        }

        if (! RansumItem::where('ransum_upload_id', $id)->exists()) {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('Tidak ada item draft. Silakan buka preview terlebih dahulu.'));
        }

        $upload->update([
            'status'      => 'imported',
            'imported_at' => now(),
        ]);

        return redirect()->route('admin.ransum.preview', $upload->id)
            ->with('success', __('Data berhasil difinalisasi ke database.'));
    }

    public function updateItem(Request $request, int $id, int $itemId)
    {
        $upload = RansumUpload::findOrFail($id);
        $item   = RansumItem::where('ransum_upload_id', $id)->findOrFail($itemId);

        $validated = $request->validate($this->itemValidationRules());
        $item->update($validated);

        return redirect()->route('admin.ransum.preview', $id)
            ->with('success', __('Item berhasil diperbarui.'));
    }

    public function destroyItem(int $id, int $itemId)
    {
        $upload = RansumUpload::findOrFail($id);
        $item   = RansumItem::where('ransum_upload_id', $id)->findOrFail($itemId);
        $item->delete();

        return redirect()->route('admin.ransum.preview', $id)
            ->with('success', __('Item berhasil dihapus.'));
    }

    private function itemValidationRules(): array
    {
        return [
            'section'         => ['nullable', 'string', 'max:255'],
            'nama_ransum'     => ['nullable', 'string', 'max:255'],
            'kode_item'       => ['nullable', 'string', 'max:255'],
            'items'           => ['nullable', 'string', 'max:255'],
            'merk_spec'       => ['nullable', 'string', 'max:255'],
            'ppn'             => ['nullable', 'numeric'],
            'harga_supplier'  => ['nullable', 'string', 'max:255'],
            'harga'           => ['nullable', 'numeric'],
            'satuan'          => ['nullable', 'string', 'max:255'],
            'qty'             => ['nullable', 'numeric'],
            'non_bkp'         => ['nullable', 'numeric'],
            'bkp'             => ['nullable', 'numeric'],
            'ppn_11'          => ['nullable', 'numeric'],
            'ket_remarks'     => ['nullable', 'string', 'max:1000'],
            'status_received' => ['nullable', 'string', 'max:255'],
            'good_received'   => ['nullable', 'string', 'max:255'],
        ];
    }

    // ------------------------------------------------------------------
    // Delivery Order (DO) Preview & Download
    // ------------------------------------------------------------------

    public function doPreview(int $id)
    {
        $upload = RansumUpload::with('items')->findOrFail($id);

        if ($upload->status !== 'imported') {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('Delivery Order hanya tersedia untuk data yang sudah diimport.'));
        }

        $grouped = [];
        foreach ($upload->items as $item) {
            $sec = $item->section ?? 'UNKNOWN';
            $grouped[$sec][] = $item;
        }
        
        return view('admin.ransum.do_preview', compact('upload', 'grouped'));
    }

    public function downloadDo(Request $request, int $id)
    {
        $upload = RansumUpload::with('items')->findOrFail($id);

        if ($upload->status !== 'imported') {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('Delivery Order hanya tersedia untuk data yang sudah diimport.'));
        }

        // Simpan data form DO ke database
        $upload->update([
            'no_do'         => $request->input('no_do'),
            'request_date'  => $request->input('request_date'),
            'delivery_date' => $request->input('delivery_date'),
            'po_number'     => $request->input('po_number'),
            'etb_jkt'       => $request->input('etb_jkt'),
            'captain'       => $request->input('captain'),
            'deliver_to'    => $request->input('deliver_to'),
        ]);

        session()->flash('success', __('Delivery Order (DO) berhasil disimpan: ') . $request->input('no_do'));

        $grouped = [];
        foreach ($upload->items as $item) {
            $sec = $item->section ?? 'UNKNOWN';
            $grouped[$sec][] = $item;
        }

        $pdf = Pdf::loadView('admin.ransum.do_pdf', compact('upload', 'grouped'))
            ->setPaper('a4', 'portrait');

        $filename = 'DO-ransum-' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $upload->vessel_name ?? $upload->id) . '.pdf';

        return $pdf->download($filename);
    }

    // ------------------------------------------------------------------
    // Invoice Preview (web) & Download (PDF)
    // ------------------------------------------------------------------

    public function invoicePreview(int $id)
    {
        $upload = RansumUpload::with('items')->findOrFail($id);

        if ($upload->status !== 'imported') {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('Invoice hanya tersedia untuk data yang sudah diimport.'));
        }
        
        // Validasi: Cek apakah no_do sudah terisi (pertanda DO sudah dibuat).
        if (empty($upload->no_do)) {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('Silakan buat Delivery Order (DO) terlebih dahulu sebelum membuat Invoice.'));
        }

                // Warning: Tampilkan peringatan jika DO belum dibuat
        if (empty($upload->no_do)) {
            session()->flash('warning', __('Peringatan: Delivery Order (DO) belum dibuat. Anda tidak dapat mendownload Invoice sebelum DO diterbitkan.'));
        }

        $grouped = [];
        foreach ($upload->items as $item) {
            $sec = $item->section ?? 'UNKNOWN';
            $grouped[$sec][] = $item;
        }
        // Ambil total dari database
         $total = $upload->total_belanja_ransum;

         // Ubah ke format huruf
         $teksTerbilang = $this->terbilang($total);
        return view('admin.ransum.invoice_preview', compact('upload', 'grouped', 'teksTerbilang'));
    }

    public function downloadInvoice(Request $request, int $id)
    {
        $upload = RansumUpload::with('items')->findOrFail($id);

        if ($upload->status !== 'imported') {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('Invoice hanya tersedia untuk data yang sudah diimport.'));
        }

                // Validasi: DO harus dibuat terlebih dahulu sebelum download invoice
        if (empty($upload->no_do)) {
            return redirect()->route('ransum.invoice', $upload->id)
                ->with('error', __('Delivery Order (DO) harus dibuat terlebih dahulu sebelum mendownload Invoice.'));
        }

        $grouped = [];
        foreach ($upload->items as $item) {
            $sec = $item->section ?? 'UNKNOWN';
            $grouped[$sec][] = $item;
        }

        $teksTerbilang = $this->terbilang($upload->total_belanja_ransum);
        $invoiceNumber = $request->input('invoice_number', 'INV-' . str_pad($upload->id, 6, '0', STR_PAD_LEFT));
        $invoiceDate   = $request->input('invoice_date', now()->format('Y-m-d'));
        $notes         = $request->input('notes', '');

        $pdf = Pdf::loadView('admin.ransum.invoice_pdf', compact('upload', 'grouped', 'invoiceNumber', 'invoiceDate', 'notes', 'teksTerbilang'))
            ->setPaper('a4', 'potrait');

        $filename = 'invoice-ransum-' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $upload->vessel_name ?? $upload->id) . '.pdf';

        return $pdf->download($filename);
    }

    private function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10). " Belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai/10)." Puluh". $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai/100) . " Ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai/1000) . " Ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai/1000000) . " Juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai/1000000000) . " Milyar" . $this->penyebut(fmod($nilai,1000000000));
        }
        return $temp;
    }

    public function terbilang($nilai) {
        if($nilai < 0) {
            $hasil = "Minus ". trim($this->penyebut($nilai));
        } else {
            $hasil = trim($this->penyebut($nilai));
        }
        return $hasil . " Rupiah";
    }

    // ------------------------------------------------------------------
    // Surat AMS (Provision Request List) Preview & Download
    // ------------------------------------------------------------------

    public function amsPreview(int $id)
    {
        $upload = RansumUpload::with('items')->findOrFail($id);

        if ($upload->status !== 'imported') {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('Surat AMS hanya tersedia untuk data yang sudah diimport.'));
        }

        return view('admin.ransum.ams_preview', compact('upload'));
    }

    public function downloadAms(Request $request, int $id)
    {
        $upload = RansumUpload::with('items')->findOrFail($id);

        if ($upload->status !== 'imported') {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('Surat AMS hanya tersedia untuk data yang sudah diimport.'));
        }

        $amsReffInput = $request->input('ams_reff');
        $pemohonInput = $request->input('pemohon');
        $menyetujuiInput = $request->input('menyetujui');

        // Save AMS fields to database
        $upload->update([
            'ams_reff'     => $request->filled('ams_reff') ? trim((string) $amsReffInput) : $upload->ams_reff,
            'pemohon'      => $request->filled('pemohon') ? trim((string) $pemohonInput) : $upload->pemohon,
            'menyetujui'   => $request->filled('menyetujui') ? trim((string) $menyetujuiInput) : $upload->menyetujui,
            'biaya_lembur' => $request->filled('biaya_lembur') ? $request->input('biaya_lembur') : 0,
            'sewa_perahu'  => $request->filled('sewa_perahu') ? $request->input('sewa_perahu') : 0,
        ]);
        $upload->refresh();

        $amsReff    = $upload->ams_reff ?? ('AMS-' . str_pad($upload->id, 5, '0', STR_PAD_LEFT));
        $biayaLembur = (float) ($upload->biaya_lembur ?? 0);
        $sewaPerahu  = (float) ($upload->sewa_perahu ?? 0);
        $totalInvoice = (float) ($upload->total_belanja_ransum ?? 0) + $biayaLembur + $sewaPerahu;

        // Compute budget per person per day
        $budgetPerOrang = null;
        $crew  = (float) ($upload->jumlah_crew ?? 0);
        $hari  = (float) ($upload->jumlah_hari_pensupplaian ?? 0);
        $totalBudget = (float) ($upload->budget ?? 0);
        if ($crew > 0 && $hari > 0 && $totalBudget > 0) {
            $budgetPerOrang = $totalBudget / $crew / $hari;
        }

        $pdf = Pdf::loadView('admin.ransum.ams_pdf', compact(
            'upload', 'amsReff', 'biayaLembur', 'sewaPerahu', 'totalInvoice', 'budgetPerOrang'
        ))->setPaper('a4', 'portrait');

        $filename = 'surat-ams-' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $upload->vessel_name ?? $upload->id) . '.pdf';

        return $pdf->download($filename);
    }

    // ------------------------------------------------------------------

    public function destroy(int $id)
    {
        $upload = RansumUpload::findOrFail($id);

        $filePath = $this->uploadDir . '/' . $upload->stored_filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Remove signature photos subfolder if it exists
        $baseName = pathinfo($upload->original_filename, PATHINFO_FILENAME);
        $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName);
        $subDir   = $this->uploadDir . '/' . $safeBase;
        if (is_dir($subDir)) {
            File::deleteDirectory($subDir);
        }

        $upload->delete();

        return redirect()->route('admin.ransum.index')
            ->with('success', __('Upload berhasil dihapus.'));
    }

    // ------------------------------------------------------------------
    // Total Preview – combined items grouped by section
    // ------------------------------------------------------------------

    public function totalPreview(int $id)
    {
        $upload = RansumUpload::with('items')->findOrFail($id);

        if ($upload->status !== 'imported') {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('Total hanya tersedia untuk data yang sudah diimport.'));
        }

        $grouped = [];
        // Map products for fast lookup
        $products = Product::with('vendor')->get()->keyBy('kode');

        $grandTotalBeli = 0;
        $grandTotalJual = 0;
        $grandTotalProfit = 0;

        foreach ($upload->items as $item) {
            $sec = $item->section ?? 'UNKNOWN';
            $kode = $item->kode_item;
            $product = $products->get($kode);
            
            $supplierName = $product?->vendor?->name ?? '-';
            // AMS HARGA JUAL is base beli (from product if available, else from item)
            $baseBeli = $product ? $product->harga_supplier : (is_numeric($item->harga_supplier) ? $item->harga_supplier : 10000);
            $baseJual = $product ? $product->harga_jual : $item->harga;
            $satuan = $product ? $product->unit : $item->satuan;
            $qty = $item->qty;

            $totalBeli = $baseBeli * $qty;
            $totalJual = $baseJual * $qty;
            $profit = $totalJual - $totalBeli;
            $percent = $totalBeli > 0 ? ($profit / $totalBeli) * 100 : 0;

            $grandTotalBeli += $totalBeli;
            $grandTotalJual += $totalJual;
            $grandTotalProfit += $profit;

            $grouped[$sec][] = [
                'kode' => $kode,
                'nama_ransum' => $item->nama_ransum,
                'items' => $item->items,
                'merk_spec' => $item->merk_spec,
                'supplier' => $supplierName,
                'ams_harga_jual' => $baseBeli,
                'ams_satuan' => $baseJual . ($satuan ? ' ' . $satuan : ''),
                'qty' => $qty,
                'pemesanan_harga_beli' => $totalBeli,
                'pemesanan_harga_jual' => $totalJual,
                'profit' => $profit,
                'percent' => $percent,
                'remarks' => $item->ket_remarks,
            ];
        }

        return view('admin.ransum.total_preview', compact('upload', 'grouped', 'grandTotalBeli', 'grandTotalJual', 'grandTotalProfit'));
    }

    public function downloadTotalPdf(Request $request, int $id)
    {
        $upload = RansumUpload::findOrFail($id);
        
        if ($upload->status !== 'imported') {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('Total hanya tersedia untuk data yang sudah diimport.'));
        }

        $htmlContent = $request->input('html_content');

        $pdf = Pdf::loadView('admin.ransum.total_pdf', compact('upload', 'htmlContent'))
            ->setPaper('a4', 'landscape');

        $filename = 'Total-Ransum-' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $upload->vessel_name ?? $upload->id) . '.pdf';

        return $pdf->download($filename);
    }

    // ------------------------------------------------------------------
    // PO Preview – items grouped per vendor from product code match (editable)
    // ------------------------------------------------------------------

    public function poPreview(int $id)
    {
        $upload = RansumUpload::with('items')->findOrFail($id);

        if ($upload->status !== 'imported') {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('PO hanya tersedia untuk data yang sudah diimport.'));
        }

        $grouped = $this->groupItemsByVendorFromProductCode($upload->items);
        $vendorDetailsBySlug = $this->buildVendorDetailsBySlug($grouped);
        $poPricesByVendor = $this->buildPoPricesByVendor($grouped);

        return view('admin.ransum.po_preview', compact('upload', 'grouped', 'vendorDetailsBySlug', 'poPricesByVendor'));
    }

public function downloadRansumPo(Request $request, int $id, string $supplierKey)
    {
        $upload = RansumUpload::with('items')->findOrFail($id);

        if ($upload->status !== 'imported') {
            return redirect()->route('admin.ransum.preview', $upload->id)
                ->with('error', __('PO hanya tersedia untuk data yang sudah diimport.'));
        }

        // ==========================================
        // SIMPAN PO NUMBER KE DATABASE SEBAGAI JSON
        // ==========================================
        if ($request->filled('po_number')) {
            $poJson = $upload->po_number;
            $poArray = (is_string($poJson) && str_starts_with(trim($poJson), '{')) ? json_decode($poJson, true) : [];
            if (!is_array($poArray)) $poArray = [];
            
            $poArray[$supplierKey] = $request->input('po_number');

            $upload->update([
                'po_number' => json_encode($poArray)
            ]);
        }

        $grouped = $this->groupItemsByVendorFromProductCode($upload->items);
        $vendorDetailsBySlug = $this->buildVendorDetailsBySlug($grouped);
        $vendorName = collect(array_keys($grouped))->first(function (string $name) use ($supplierKey) {
            return Str::slug($name) === $supplierKey;
        });

        if ($vendorName === null) {
            return redirect()->route('admin.ransum.po.preview', $upload->id)
                ->with('error', __('Tidak ada item untuk vendor ini.'));
        }

        $items = collect($grouped[$vendorName])->values();
        $supplierName = $vendorName;
        $vendorDetails = $vendorDetailsBySlug[$supplierKey] ?? [
            'name' => $vendorName,
            'contact_name' => '',
            'address' => '',
            'phone' => '',
            'email' => '',
        ];

        $formData = [
            'po_number' => $request->input('po_number'),
            'po_date' => $request->input('po_date'),
            'delivery_date' => $request->input('delivery_date'),
            'vessel_name' => $request->input('vessel_name'),
            'voyage' => $request->input('voyage'),
            'vendor_name' => $request->input('vendor_name', $vendorDetails['name']),
            'vendor_contact_name' => $request->input('vendor_contact_name', $vendorDetails['contact_name']),
            'vendor_address' => $request->input('vendor_address', $vendorDetails['address']),
            'vendor_phone' => $request->input('vendor_phone', $vendorDetails['phone']),
            'vendor_email' => $request->input('vendor_email', $vendorDetails['email']),
            'deliver_to' => $request->input('deliver_to'),
            'notes' => $request->input('notes'),
            'prepared_by' => $request->input('prepared_by'),
            'approved_by' => $request->input('approved_by'),
        ];

        $editedItems = [];
        $grandTotal  = 0;
        foreach ($items as $idx => $item) {
            $qty    = max(0, (float) $request->input("items.{$idx}.qty", $item->qty));
            $harga  = max(0, (float) $request->input("items.{$idx}.harga", $this->resolvePoUnitPrice($item)));
            $sub    = $qty * $harga;
            $grandTotal += $sub;
            $editedItems[] = [
                'nama_ransum' => $request->input("items.{$idx}.nama_ransum", $item->nama_ransum),
                'satuan'      => $request->input("items.{$idx}.satuan", $item->satuan),
                'qty'         => $qty,
                'harga'       => $harga,
                'subtotal'    => $sub,
                'keterangan'  => $request->input("items.{$idx}.keterangan", ''),
            ];
        }

        $pdf = Pdf::loadView('admin.ransum.po_pdf', compact('upload', 'supplierName', 'formData', 'editedItems', 'grandTotal'))
            ->setPaper('a4', 'portrait');

        $filename = 'PO-ransum-' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $upload->vessel_name ?? $upload->id) . '-' . $supplierKey . '.pdf';

        return $pdf->download($filename);
    }

    private function buildVendorDetailsBySlug(array $grouped): array
    {
        $vendorNames = array_map(fn ($name) => trim((string) $name), array_keys($grouped));

        $vendorsByName = Vendor::query()
            ->select(['name', 'contact_name', 'address', 'phone', 'email'])
            ->whereIn('name', $vendorNames)
            ->get()
            ->keyBy(fn (Vendor $vendor) => Str::lower(trim((string) $vendor->name)));

        $details = [];
        foreach (array_keys($grouped) as $vendorName) {
            $slug = Str::slug($vendorName);
            $normalizedVendorName = Str::lower(trim((string) $vendorName));
            $vendor = $vendorsByName->get($normalizedVendorName);
            $details[$slug] = [
                'name' => $vendor?->name ?? $vendorName,
                'contact_name' => $vendor?->contact_name ?? '',
                'address' => $vendor?->address ?? '',
                'phone' => $vendor?->phone ?? '',
                'email' => $vendor?->email ?? '',
            ];
        }

        return $details;
    }

    private function buildPoPricesByVendor(array $grouped): array
    {
        $prices = [];
        foreach ($grouped as $vendorName => $items) {
            $vendorSlug = Str::slug($vendorName);
            foreach ($items as $idx => $item) {
                $prices[$vendorSlug][$idx] = $this->resolvePoUnitPrice($item);
            }
        }

        return $prices;
    }

    private function resolvePoUnitPrice(RansumItem $item): float
    {
        $supplierRaw = $item->harga_supplier;
        if (is_numeric($supplierRaw)) {
            return max(0, (float) $supplierRaw);
        }

        $supplierNormalized = $this->normalizeMoneyString((string) $supplierRaw);
        if ($supplierNormalized !== null && is_numeric($supplierNormalized)) {
            return max(0, (float) $supplierNormalized);
        }

        return max(0, (float) ($item->harga ?? 0));
    }

    private function normalizeMoneyString(string $value): ?string
    {
        $clean = preg_replace('/[^0-9.,]/', '', $value);
        if ($clean === '') {
            return null;
        }

        $commaCount = substr_count($clean, ',');
        $dotCount = substr_count($clean, '.');

        if ($commaCount > 0 && $dotCount > 0) {
            $lastComma = strrpos($clean, ',');
            $lastDot = strrpos($clean, '.');
            $decimalSeparator = $lastComma > $lastDot ? ',' : '.';
            $thousandSeparator = $decimalSeparator === ',' ? '.' : ',';

            $normalized = str_replace($thousandSeparator, '', $clean);
            if ($decimalSeparator === ',') {
                $normalized = str_replace(',', '.', $normalized);
            }

            return $normalized;
        }

        if ($commaCount > 0) {
            if ($commaCount === 1) {
                $parts = explode(',', $clean);
                // Heuristic: treat single comma with up to 2 trailing digits as decimal comma (e.g. "10,50"),
                // and otherwise as thousand separator (e.g. "1,000").
                if (count($parts) === 2 && strlen($parts[1]) <= self::PRICE_DECIMAL_HEURISTIC_MAX_SCALE) {
                    return $parts[0] . '.' . $parts[1];
                }
            }

            return str_replace(',', '', $clean);
        }

        if ($dotCount > 1) {
            return str_replace('.', '', $clean);
        }

        return $clean;
    }

    private function groupItemsByVendorFromProductCode($items): array
    {
        $codes = collect($items)
            ->pluck('kode_item')
            ->map(fn ($code) => $this->normalizeProductCode($code))
            ->filter()
            ->unique()
            ->values();

        $productsByCode = collect();
        if ($codes->isNotEmpty()) {
            $productsByCode = Product::with('vendor:id,name')
                ->whereIn('kode', $codes)
                ->get()
                ->keyBy(fn (Product $product) => $this->normalizeProductCode($product->kode));
        }

        $grouped = [];
        foreach ($items as $item) {
            $vendorName = $this->resolveVendorNameFromCode($item->kode_item, $productsByCode);
            $grouped[$vendorName][] = $item;
        }

        ksort($grouped);

        return $grouped;
    }

    private function resolveVendorNameFromCode(?string $kodeItem, $productsByCode): string
    {
        $normalizedCode = $this->normalizeProductCode($kodeItem);
        if ($normalizedCode === null) {
            return 'UNKNOWN';
        }

        $product = $productsByCode->get($normalizedCode);
        if ($product === null || $product->vendor === null) {
            return 'UNKNOWN';
        }

        $vendorName = trim((string) $product->vendor->name);

        return $vendorName !== '' ? $vendorName : 'UNKNOWN';
    }

    private function normalizeProductCode(?string $code): ?string
    {
        if ($code === null) {
            return null;
        }

        $normalized = strtoupper(trim((string) $code));

        return $normalized !== '' ? $normalized : null;
    }
}