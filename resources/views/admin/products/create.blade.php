<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Add Product') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ route('admin.products.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="vendor_id" :value="__('Vendor')" />
                            <select id="vendor_id" name="vendor_id" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">{{ __('-- Select Vendor --') }}</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('vendor_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="kode" :value="__('Kode Produk')" />
                            <x-text-input id="kode" name="kode" type="text" class="mt-1 block w-full" :value="old('kode')" placeholder="e.g. BK010002" />
                            <x-input-error :messages="$errors->get('kode')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="name" :value="__('Product Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="category" :value="__('Category')" />
                            <x-text-input id="category" name="category" type="text" class="mt-1 block w-full" :value="old('category')" placeholder="e.g. Dry Goods, Beverages, Dairy" />
                        </div>
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('description') }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="harga_supplier" :value="__('Harga Supplier (Rp)')" />
                                <x-text-input id="harga_supplier" name="harga_supplier" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('harga_supplier')" />
                                <x-input-error :messages="$errors->get('harga_supplier')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="harga_jual" :value="__('Harga Jual (Rp)')" />
                                <x-text-input id="harga_jual" name="harga_jual" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('harga_jual')" required />
                                <x-input-error :messages="$errors->get('harga_jual')" class="mt-2" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="unit" :value="__('Unit')" />
                            <x-text-input id="unit" name="unit" type="text" class="mt-1 block w-full" :value="old('unit', 'pcs')" required placeholder="pcs, kg, box..." />
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <x-input-label for="is_active" :value="__('Active (visible to customers)')" class="mb-0 cursor-pointer" />
                        </div>
                    </div>
                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>{{ __('Add Product') }}</x-primary-button>
                        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
