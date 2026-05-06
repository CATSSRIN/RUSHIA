<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order #{{ $order->id }}</h2>
            <div class="flex flex-wrap gap-3 items-center">
                <a href="{{ route('admin.orders.invoice', $order) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ __('Download Invoice') }}
                </a>
                @php $headerVendors = $order->items->map(fn($i) => $i->product?->vendor)->filter()->unique('id')->values(); @endphp
                @foreach($headerVendors as $vendor)
                    <a href="{{ route('admin.orders.po.preview', [$order, $vendor]) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        {{ __('Surat PO') }} – {{ $vendor->name }}
                    </a>
                @endforeach
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">← {{ __('Back') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            <!-- Order Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase">{{ __('Company') }}</p>
                        <p class="font-semibold text-gray-800 mt-1">{{ $order->user->company_name ?? $order->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $order->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">{{ __('Ship') }}</p>
                        <p class="font-semibold text-gray-800 mt-1">{{ $order->ship->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">{{ __('Date') }}</p>
                        <p class="font-semibold text-gray-800 mt-1">{{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
                @if($order->notes)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-400 uppercase">{{ __('Notes') }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $order->notes }}</p>
                    </div>
                @endif
                @if($order->pickup_date || $order->pickup_time || $order->pickup_location)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-400 uppercase">{{ __('Informasi Pengambilan') }}</p>
                        <div class="mt-1 text-sm text-gray-600 space-y-0.5">
                            @if($order->pickup_date)
                                <div>{{ \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') }}{{ $order->pickup_time ? ' pukul ' . \Carbon\Carbon::parse($order->pickup_time)->format('H:i') : '' }}</div>
                            @endif
                            @if($order->pickup_location)
                                <div>{{ $order->pickup_location }}</div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Status Update -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-700 mb-3">{{ __('Update Status') }}</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(['pending','confirmed','delivered','cancelled'] as $status)
                        <form method="POST" action="{{ route('admin.orders.status', [$order, $status]) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-4 py-2 text-sm rounded-lg border transition {{ $order->status === $status ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }} capitalize">{{ $status }}</button>
                        </form>
                    @endforeach
                </div>
            </div>

            <!-- Surat PO -->
            @php $poVendors = $order->items->map(fn($i) => $i->product?->vendor)->filter()->unique('id')->values(); @endphp
            @if($poVendors->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-700 mb-3">{{ __('Surat PO') }}</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($poVendors as $vendor)
                        <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2 bg-gray-50">
                            <div>
                                <p class="text-xs text-gray-500 font-medium">{{ $vendor->name }}</p>
                                <p class="text-xs text-gray-400">PO-{{ str_pad($order->id,5,'0',STR_PAD_LEFT) }}-{{ $vendor->id }}</p>
                            </div>
                            <a href="{{ route('admin.orders.po.preview', [$order, $vendor]) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Preview & Download
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Items -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-700">{{ __('Order Items') }}</h3>
                </div>
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-50">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Product') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Vendor') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Unit Price') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Qty') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-3 text-sm text-gray-800">{{ $item->product->name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-500">{{ $item->product->vendor->name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-800 text-right">Rp {{ number_format($item->unit_price, 0, ",", ".") }}</td>
                            <td class="px-6 py-3 text-sm text-gray-800 text-right">{{ $item->quantity }}</td>
                            <td class="px-6 py-3 text-sm font-semibold text-gray-900 text-right">Rp {{ number_format($item->subtotal, 0, ",", ".") }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-6 py-3 text-right font-semibold text-gray-700">{{ __('Total') }}</td>
                            <td class="px-6 py-3 text-right font-bold text-indigo-600 text-base">Rp {{ number_format($order->total_price, 0, ",", ".") }}</td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
