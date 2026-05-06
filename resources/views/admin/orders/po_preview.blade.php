<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Buat Purchase Order (PO)') }}: Order #{{ $order->id }} — {{ $order->user->company_name ?? $order->user->name }}
            </h2>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            <!-- Order Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-3">{{ __('Informasi Order') }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="block text-xs font-medium text-gray-400 uppercase">{{ __('Order #') }}</span>
                        <span class="font-semibold text-gray-800">#{{ $order->id }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-medium text-gray-400 uppercase">{{ __('Perusahaan') }}</span>
                        <span class="font-semibold text-gray-800">{{ $order->user->company_name ?? $order->user->name }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-medium text-gray-400 uppercase">{{ __('Kapal') }}</span>
                        <span class="font-semibold text-gray-800">{{ $order->ship->name }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-medium text-gray-400 uppercase">{{ __('Tanggal') }}</span>
                        <span class="font-semibold text-gray-800">{{ $order->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            @if(empty($byVendor))
                <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
                    <p class="text-gray-400">{{ __('Order ini tidak memiliki item.') }}</p>
                </div>
            @else
                @foreach($byVendor as $vendorId => $group)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Vendor Header -->
                        <div class="px-6 py-4 bg-orange-50 border-b border-orange-100 flex items-center justify-between">
                            <div>
                                <h4 class="text-base font-semibold text-orange-900">{{ $group['vendor']->name }}</h4>
                                @if($group['vendor']->address)
                                    <p class="text-xs text-orange-600 mt-0.5">{{ $group['vendor']->address }}</p>
                                @endif
                            </div>
                            <a href="{{ route('admin.orders.po.download', [$order, $group['vendor']->id]) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-lg hover:bg-orange-700 transition whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ __('Download PO') }}
                            </a>
                        </div>

                        <!-- Items Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Produk') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Satuan') }}</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Qty') }}</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Harga Satuan') }}</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($group['items'] as $i => $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                                            <td class="px-4 py-3 text-gray-800 font-medium">{{ $item->product->name }}</td>
                                            <td class="px-4 py-3 text-gray-500">{{ $item->product->unit ?? '-' }}</td>
                                            <td class="px-4 py-3 text-right text-gray-800">{{ $item->quantity }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-orange-50">
                                        <td colspan="5" class="px-4 py-3 text-right font-semibold text-gray-700">{{ __('Total') }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-orange-700">Rp {{ number_format($group['subtotal'], 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>
    </div>
</x-app-layout>
