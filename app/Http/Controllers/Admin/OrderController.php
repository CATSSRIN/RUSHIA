<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrderTotalSheetExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RansumUpload;
use App\Models\Vendor;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user', 'ship', 'items.product.vendor', 'pos')->latest()->get();
        $ransumOrders = RansumUpload::with('pos', 'items')
            ->whereNotNull('no_do')
            ->where('no_do', '!=', '')
            ->orderByDesc('created_at')
            ->get();

        // Group Ransum items by vendor name in a single efficient query
        $codes = $ransumOrders->flatMap(fn($r) => $r->items->pluck('kode_item'))
            ->map(fn($c) => strtoupper(trim((string) $c)))
            ->filter()
            ->unique()
            ->values();

        $productsByCode = collect();
        if ($codes->isNotEmpty()) {
            $productsByCode = Product::with('vendor')
                ->whereIn('kode', $codes)
                ->get()
                ->keyBy(fn($p) => strtoupper(trim((string) $p->kode)));
        }

        foreach ($ransumOrders as $ransum) {
            $grouped = [];
            foreach ($ransum->items as $item) {
                $normalizedCode = strtoupper(trim((string) $item->kode_item));
                $product = $normalizedCode !== '' ? $productsByCode->get($normalizedCode) : null;
                $vendorName = ($product && $product->vendor) ? trim((string) $product->vendor->name) : 'UNKNOWN';
                if ($vendorName === '') {
                    $vendorName = 'UNKNOWN';
                }
                $grouped[$vendorName][] = $item;
            }
            ksort($grouped);
            $ransum->grouped_items_by_vendor = $grouped;
        }

        return view('admin.orders.index', compact('orders', 'ransumOrders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'ship', 'items.product.vendor', 'pos');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Order $order, string $status)
    {
        $order->update(['status' => $status]);
        return back()->with('success', 'Order status updated.');
    }

    public function downloadInvoice(Order $order)
    {
        $order->load('user', 'ship', 'items.product.vendor');
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
        return $pdf->download('invoice-order-' . $order->id . '.pdf');
    }

    public function downloadTotalSheet(Order $order)
    {
        $order->load('user', 'ship', 'items.product.vendor');

        return Excel::download(
            new OrderTotalSheetExport($order),
            'hasil-total-harga-order-' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) . '.xlsx'
        );
    }

    public function poPreview(Order $order, Vendor $vendor)
    {
        $order->load('user', 'ship', 'items.product.vendor');
        $items = $order->items->filter(fn($item) => $item->product?->vendor_id == $vendor->id)->values();

        if ($items->isEmpty()) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Tidak ada item untuk vendor ini pada order tersebut.');
        }

        return view('admin.orders.po_preview', compact('order', 'vendor', 'items'));
    }

public function downloadPo(Request $request, Order $order, Vendor $vendor)
    {
        $order->load('user', 'ship', 'items.product.vendor');
        $items = $order->items->filter(fn($item) => $item->product?->vendor_id == $vendor->id)->values();

        if ($items->isEmpty()) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Tidak ada item untuk vendor ini pada order tersebut.');
        }

        $data = $request->only([
            'po_number', 'po_date', 'delivery_date', 'ship_name', 'company_name',
            'vendor_name', 'vendor_address', 'vendor_phone', 'vendor_email',
            'deliver_to', 'notes', 'prepared_by', 'approved_by',
        ]);

        // ==========================================
        // SIMPAN PO NUMBER KE DATABASE SEBAGAI JSON
        // ==========================================
        if ($request->filled('po_number')) {
            $poJson = $order->po_number;
            $poArray = (is_string($poJson) && str_starts_with(trim($poJson), '{')) ? json_decode($poJson, true) : [];
            if (!is_array($poArray)) $poArray = [];
            
            $vendorSlug = Str::slug($vendor->name);
            $poArray[$vendorSlug] = $request->input('po_number');

            $order->update([
                'po_number' => json_encode($poArray)
            ]);
        }

        // Merge edited item rows from the form
        $editedItems = [];
        $totalPrice = 0;
        foreach ($items as $idx => $item) {
            $qty   = max(0, (float) $request->input("items.{$idx}.quantity", $item->quantity));
            $price = max(0, (float) $request->input("items.{$idx}.unit_price", $item->unit_price));
            $sub   = $qty * $price;
            $totalPrice += $sub;
            $editedItems[] = [
                'name'       => $request->input("items.{$idx}.name", $item->product->name),
                'unit'       => $request->input("items.{$idx}.unit", $item->product->unit ?? 'pcs'),
                'quantity'   => $qty,
                'unit_price' => $price,
                'subtotal'   => $sub,
                'notes'      => $request->input("items.{$idx}.notes", ''),
            ];
        }

        $pdf = Pdf::loadView('admin.orders.po_pdf', array_merge($data, [
            'order'       => $order,
            'vendor'      => $vendor,
            'items'       => collect($editedItems),
            'total_price' => $totalPrice,
        ]))->setPaper('a4', 'portrait');

        $filename = 'PO-' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . '-' . Str::slug($vendor->name) . '.pdf';

        // Save PDF to storage
        $pdfDir = storage_path('app/private/order_pos');
        \Illuminate\Support\Facades\File::ensureDirectoryExists($pdfDir, 0755);
        \Illuminate\Support\Facades\File::put($pdfDir . '/' . $filename, $pdf->output());

        // Create or update OrderPo record
        \App\Models\OrderPo::updateOrCreate(
            [
                'order_id' => $order->id,
                'vendor_id' => $vendor->id,
            ],
            [
                'po_number' => $request->input('po_number', 'PO-' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . '-' . $vendor->id),
                'pdf_path' => $filename,
            ]
        );

        return $pdf->download($filename);
    }

    public function serveSavedPoPdf(int $id)
    {
        $po = \App\Models\OrderPo::findOrFail($id);
        $fullPath = storage_path('app/private/order_pos/' . $po->pdf_path);
        
        if (!file_exists($fullPath)) {
            abort(404);
        }

        return view('admin.po_saved_preview', [
            'poNumber' => $po->po_number,
            'streamUrl' => route('admin.orders.po.stream_saved', $po->id),
            'downloadUrl' => route('admin.orders.po.download_saved', $po->id),
            'backUrl' => route('admin.orders.index'),
        ]);
    }

    public function streamSavedPoPdf(int $id)
    {
        $po = \App\Models\OrderPo::findOrFail($id);
        $fullPath = storage_path('app/private/order_pos/' . $po->pdf_path);
        
        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline'
        ]);
    }

    public function downloadSavedPoPdf(int $id)
    {
        $po = \App\Models\OrderPo::findOrFail($id);
        $fullPath = storage_path('app/private/order_pos/' . $po->pdf_path);
        
        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->download($fullPath, $po->pdf_path);
    }

    public function updatePoStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:menunggu,diproses,selesai'],
        ]);

        $po = \App\Models\OrderPo::findOrFail($id);
        $po->update([
            'status' => $request->input('status')
        ]);

        return back()->with('success', 'Status PO berhasil diperbarui.');
    }
}
