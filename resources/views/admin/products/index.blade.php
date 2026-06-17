<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Products') }}</h2>
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 bg-[#217a68] hover:bg-[#1b6455] text-white text-sm font-medium rounded-lg transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Add Product') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            <!-- Search Bar -->
            <form method="GET" action="{{ route('admin.products.index') }}" class="mb-6">
                <div class="flex gap-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="{{ __('Search product, code, or vendor...') }}"
                        class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    />
                    <button type="submit" class="px-4 py-2 bg-[#217a68] hover:bg-[#1b6455] text-white text-sm font-medium rounded-lg transition shadow-sm">{{ __('Search') }}</button>
                    @if($search)
                        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">{{ __('Clear') }}</a>
                    @endif
                </div>
            </form>

            @if($products->isEmpty())
                <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                    <p class="text-gray-400">{{ __('No products found.') }}</p>
                </div>
            @else
                @foreach($products as $vendorName => $vendorProducts)
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-[#217a68] uppercase tracking-wide mb-2 px-1">{{ $vendorName }}</h3>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Kode') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Product') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Vendor') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Category') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Harga Supplier') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Harga Jual') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($vendorProducts as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 text-sm text-gray-500">
                                        @if($product->kode)
                                            <span class="font-mono">{{ $product->kode }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                        @if($product->description)<p class="text-xs text-gray-400">{{ Str::limit($product->description, 50) }}</p>@endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $product->vendor->name }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $product->category ?? '—' }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-700 text-right">
                                        @if($product->harga_supplier !== null)
                                            Rp {{ number_format($product->harga_supplier, 0, ',', '.') }}<span class="text-xs font-normal text-gray-400">/{{ $product->unit }}</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm font-semibold text-gray-900 text-right">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}<span class="text-xs font-normal text-gray-400">/{{ $product->unit }}</span></td>
                                    <td class="px-4 py-4">
                                        @if($product->is_active)
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ __('Active') }}</span>
                                        @else
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex justify-end items-center gap-2">
                                            <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center justify-center rounded-lg border border-[#217a68]/20 bg-[#217a68]/5 px-3 py-1.5 text-xs font-bold text-[#217a68] hover:bg-[#217a68]/10 transition shadow-sm gap-1">
                                                {{ __('Edit') }}
                                            </a>
                                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('{{ __('Delete this product?') }}')" class="inline-block">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 transition shadow-sm gap-1">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
