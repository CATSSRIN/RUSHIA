<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user', 'ship')->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'ship', 'items.product.vendor');
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

    public function poPreview(Order $order)
    {
        $order->load('user', 'ship', 'items.product.vendor');

        $byVendor = [];
        foreach ($order->items as $item) {
            $vendor = $item->product->vendor ?? null;
            if ($vendor === null) {
                continue;
            }
            $vendorId = $vendor->id;
            if (!isset($byVendor[$vendorId])) {
                $byVendor[$vendorId] = [
                    'vendor'   => $vendor,
                    'items'    => [],
                    'subtotal' => 0,
                ];
            }
            $byVendor[$vendorId]['items'][]  = $item;
            $byVendor[$vendorId]['subtotal'] += $item->subtotal;
        }

        return view('admin.orders.po_preview', compact('order', 'byVendor'));
    }

    public function downloadPo(Order $order, Vendor $vendor)
    {
        $order->load('user', 'ship', 'items.product.vendor');

        $items    = $order->items->filter(fn($item) => $item->product->vendor !== null && $item->product->vendor_id === $vendor->id)->values();

        if ($items->isEmpty()) {
            return redirect()->route('admin.orders.po.preview', $order)->with('error', __('Tidak ada item untuk vendor ini.'));
        }

        $subtotal = $items->sum('subtotal');

        $pdf = Pdf::loadView('admin.orders.po_pdf', compact('order', 'vendor', 'items', 'subtotal'))
            ->setPaper('a4', 'portrait');

        $filename = 'PO-order-' . $order->id . '-' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $vendor->name) . '.pdf';

        return $pdf->download($filename);
    }
}
