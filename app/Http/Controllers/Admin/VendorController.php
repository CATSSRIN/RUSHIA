<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::withCount('products')->latest()->get();
        return view('admin.vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('admin.vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
        ]);

        $vendor = Vendor::create($request->only('name', 'contact_name', 'email', 'phone', 'address'));

        \App\Models\ActivityLog::log('create_vendor', 'Menambahkan vendor baru: ' . $vendor->name);

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor added successfully.');
    }

    public function edit(Vendor $vendor)
    {
        return view('admin.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
        ]);

        $vendor->update($request->only('name', 'contact_name', 'email', 'phone', 'address'));

        \App\Models\ActivityLog::log('update_vendor', 'Memperbarui vendor: ' . $vendor->name);

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor updated.');
    }

    public function destroy(Vendor $vendor)
    {
        \App\Models\ActivityLog::log('delete_vendor', 'Menghapus vendor: ' . $vendor->name);

        $vendor->delete();
        return redirect()->route('admin.vendors.index')->with('success', 'Vendor removed.');
    }

    public function show(Vendor $vendor)
    {
        //
    }
}
