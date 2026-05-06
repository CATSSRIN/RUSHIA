<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('All Orders') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">{{ session('error') }}</div>
            @endif

            @if($orders->isEmpty())
                <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                    <p class="text-gray-400">{{ __('No orders yet.') }}</p>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Order #') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Company') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Ship') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Total') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Pickup') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Surat PO') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($orders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $order->user->company_name ?? $order->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $order->ship->name }}</td>
                                <td class="px-6 py-4 text-sm font-semibold">Rp {{ number_format($order->total_price, 0, ",", ".") }}</td>
                                <td class="px-6 py-4">
                                    @php $statusCls = match($order->status) { 'confirmed' => 'bg-blue-100 text-blue-700', 'delivered' => 'bg-green-100 text-green-700', 'cancelled' => 'bg-red-100 text-red-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusCls }} capitalize">{{ $order->status }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($order->pickup_date || $order->pickup_location)
                                        <div class="space-y-0.5">
                                            @if($order->pickup_date)
                                                <div>{{ \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') }}{{ $order->pickup_time ? ' ' . \Carbon\Carbon::parse($order->pickup_time)->format('H:i') : '' }}</div>
                                            @endif
                                            @if($order->pickup_location)
                                                <div class="text-xs text-gray-400">{{ $order->pickup_location }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $vendors = $order->items->map(fn($i) => $i->product->vendor)->unique('id')->values();
                                    @endphp
                                    @if($vendors->isNotEmpty())
                                        <div class="flex flex-col gap-1">
                                            @foreach($vendors as $vendor)
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-xs text-gray-600 truncate max-w-[120px]" title="{{ $vendor->name }}">
                                                        PO-{{ str_pad($order->id,5,'0',STR_PAD_LEFT) }}-{{ $vendor->id }}
                                                    </span>
                                                    <a href="{{ route('admin.orders.po.preview', [$order, $vendor]) }}"
                                                       class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-medium rounded transition whitespace-nowrap">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Preview
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">{{ __('View') }}</a>
                                        <a href="{{ route('admin.orders.invoice', $order) }}" class="text-green-600 hover:text-green-800 text-sm font-medium">{{ __('PDF') }}</a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

