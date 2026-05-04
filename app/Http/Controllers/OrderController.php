<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()->with('ship')->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $ships = Auth::user()->ships()->get();
        if ($ships->isEmpty()) {
            return redirect()->route('ships.create')->with('info', 'Please add a ship first before placing an order.');
        }
        $vendors = Vendor::with(['products' => function ($q) {
            $q->where('is_active', true);
        }])->get();
        return view('orders.create', compact('ships', 'vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ship_id' => ['required', 'exists:ships,id'],
            'notes' => ['nullable', 'string'],
            'pickup_date' => ['nullable', 'date'],
            'pickup_time' => ['nullable', 'date_format:H:i'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        // Ensure ship belongs to user
        $ship = Auth::user()->ships()->findOrFail($request->ship_id);

        DB::transaction(function () use ($request, $ship) {
            $total = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->harga_jual * $item['quantity'];
                $total += $subtotal;
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->harga_jual,
                    'subtotal' => $subtotal,
                ];
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'ship_id' => $ship->id,
                'total_price' => $total,
                'status' => 'pending',
                'notes' => $request->notes,
                'pickup_date' => $request->pickup_date,
                'pickup_time' => $request->pickup_time,
                'pickup_location' => $request->pickup_location,
            ]);

            foreach ($orderItems as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }
        });

        return redirect()->route('orders.index')->with('success', 'Order placed successfully!');
    }

    public function updatePickup(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'pickup_date'     => ['nullable', 'date'],
            'pickup_time'     => ['nullable', 'date_format:H:i'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
        ]);

        $order->update([
            'pickup_date'     => $request->pickup_date ?: null,
            'pickup_time'     => $request->pickup_time ?: null,
            'pickup_location' => $request->pickup_location ?: null,
        ]);

        return redirect()->route('orders.index')->with('success', 'Shipping schedule updated successfully.');
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->load('ship', 'items.product.vendor');
        return view('orders.show', compact('order'));
    }
}
