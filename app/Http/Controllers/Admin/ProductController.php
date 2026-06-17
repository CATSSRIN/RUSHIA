<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Product::with('vendor')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhereHas('vendor', fn($v) => $v->where('name', 'like', "%{$search}%"));
            });
        }

        $products = $query->get()->groupBy(fn($p) => $p->vendor->name ?? 'Unknown');

        return view('admin.products.index', compact('products', 'search'));
    }

    public function create()
    {
        $vendors = Vendor::orderBy('name')->get();
        return view('admin.products.create', compact('vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => ['required', 'exists:vendors,id'],
            'kode' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'harga_supplier' => ['nullable', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $product = Product::create($request->only('vendor_id', 'kode', 'name', 'category', 'description', 'harga_supplier', 'harga_jual', 'unit') + [
            'is_active' => $request->boolean('is_active', true),
        ]);

        \App\Models\ActivityLog::log('create_product', 'Menambahkan produk baru: ' . $product->name . ' (Kode: ' . ($product->kode ?? '-') . ')');

        return redirect()->route('admin.products.index')->with('success', 'Product added successfully.');
    }

    public function edit(Product $product)
    {
        $vendors = Vendor::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'vendors'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'vendor_id' => ['required', 'exists:vendors,id'],
            'kode' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'harga_supplier' => ['nullable', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
        ]);

        $product->update($request->only('vendor_id', 'kode', 'name', 'category', 'description', 'harga_supplier', 'harga_jual', 'unit') + [
            'is_active' => $request->boolean('is_active'),
        ]);

        \App\Models\ActivityLog::log('update_product', 'Memperbarui produk: ' . $product->name . ' (Kode: ' . ($product->kode ?? '-') . ')');

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        \App\Models\ActivityLog::log('delete_product', 'Menghapus produk: ' . $product->name . ' (Kode: ' . ($product->kode ?? '-') . ')');

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product removed.');
    }

    public function show(Product $product)
    {
        //
    }
}
