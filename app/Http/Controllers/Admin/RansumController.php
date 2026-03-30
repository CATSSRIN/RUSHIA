<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\RansumImport;
use App\Imports\RansumParser;
use App\Models\RansumItem;
use App\Models\RansumUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class RansumController extends Controller
{
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

        $filePath = $this->uploadDir . '/' . $upload->stored_filename;

        if (! file_exists($filePath)) {
            return redirect()->route('admin.ransum.index')
                ->with('error', __('File tidak ditemukan.'));
        }

        try {
            $import = new RansumImport();
            Excel::import($import, $filePath);
            $parser   = new RansumParser($import->getData());
            $sections = $parser->parseItems();
        } catch (\Throwable $e) {
            return redirect()->route('admin.ransum.index')
                ->with('error', __('Gagal membaca file: ') . $e->getMessage());
        }

        return view('admin.ransum.preview', compact('upload', 'sections'));
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
    // Destroy – delete upload record + file
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
}
