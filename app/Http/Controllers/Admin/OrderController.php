<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user', 'ship', 'items.product.vendor')->latest()->get();
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

    public function poPreview(Order $order, Vendor $vendor)
    {
        $order->load('user', 'ship', 'items.product.vendor');
        $items = $order->items->filter(fn($item) => $item->product->vendor_id === $vendor->id)->values();

        if ($items->isEmpty()) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Tidak ada item untuk vendor ini pada order tersebut.');
        }

        return view('admin.orders.po_preview', compact('order', 'vendor', 'items'));
    }

    public function downloadPo(Request $request, Order $order, Vendor $vendor)
    {
        $order->load('user', 'ship', 'items.product.vendor');
        $items = $order->items->filter(fn($item) => $item->product->vendor_id === $vendor->id)->values();

        if ($items->isEmpty()) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Tidak ada item untuk vendor ini pada order tersebut.');
        }

        $data = $request->only([
            'po_number', 'po_date', 'delivery_date', 'ship_name', 'company_name',
            'vendor_name', 'vendor_address', 'vendor_phone', 'vendor_email',
            'deliver_to', 'notes', 'prepared_by', 'approved_by',
        ]);

        // Merge edited item rows from the form — recalculate subtotal server-side
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

        return $pdf->download($filename);
    }
}
