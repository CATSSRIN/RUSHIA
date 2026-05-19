<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('All Orders') }}</h2>
            <p class="text-sm text-gray-500">Pantau seluruh pesanan customer dan dokumen PO dalam satu halaman.</p>
        </div>
    </x-slot>

    @php
        $statusClasses = [
            'pending' => 'bg-amber-100 text-amber-700',
            'confirmed' => 'bg-blue-100 text-blue-700',
            'delivered' => 'bg-emerald-100 text-emerald-700',
            'cancelled' => 'bg-red-100 text-red-700',
        ];

        $statusLabels = [
            'pending' => 'Menunggu',
            'confirmed' => 'Diproses',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $summaryTotals = $orders->reduce(function ($carry, $order) {
            $carry['total']++;
            $carry['pending'] += $order->status === 'pending' ? 1 : 0;
            $carry['confirmed'] += $order->status === 'confirmed' ? 1 : 0;
            $carry['delivered'] += $order->status === 'delivered' ? 1 : 0;
            $carry['value'] += (float) $order->total_price;

            return $carry;
        }, [
            'total' => 0,
            'pending' => 0,
            'confirmed' => 0,
            'delivered' => 0,
            'value' => 0,
        ]);

        $orderSummary = [
            ['label' => 'Total Pesanan', 'value' => $summaryTotals['total'], 'tone' => 'text-slate-900'],
            ['label' => 'Menunggu', 'value' => $summaryTotals['pending'], 'tone' => 'text-amber-600'],
            ['label' => 'Diproses', 'value' => $summaryTotals['confirmed'], 'tone' => 'text-blue-600'],
            ['label' => 'Selesai', 'value' => $summaryTotals['delivered'], 'tone' => 'text-emerald-600'],
            ['label' => 'Nilai Pesanan', 'value' => 'Rp ' . number_format($summaryTotals['value'], 0, ',', '.'), 'tone' => 'text-indigo-600'],
        ];

        $formatQuantity = function ($quantity) {
            $value = (float) $quantity;

            return fmod($value, 1.0) === 0.0
                ? number_format($value, 0, ',', '.')
                : number_format($value, 2, ',', '.');
        };
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                @foreach($orderSummary as $summary)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <p class="text-sm text-gray-500">{{ $summary['label'] }}</p>
                        <p class="mt-2 text-2xl font-semibold {{ $summary['tone'] }}">{{ $summary['value'] }}</p>
                    </div>
                @endforeach
            </div>

            @if($orders->isEmpty())
                <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-50 text-gray-300">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7.5h18M6.75 7.5l.68 10.88A2.25 2.25 0 009.67 20.5h4.66a2.25 2.25 0 002.24-2.12l.68-10.88M9.75 11.25v5.25m4.5-5.25v5.25M9 4.5h6a1.5 1.5 0 011.5 1.5v1.5h-9V6A1.5 1.5 0 019 4.5z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-700">Belum ada pesanan.</h3>
                    <p class="mt-2 text-sm text-gray-400">Pesanan yang masuk dari customer akan tampil di halaman ini.</p>
                </div>
            @else
                <div class="space-y-5">
                    @foreach($orders as $order)
                        @php
                            $vendors = $order->items->map(fn($item) => $item->product?->vendor)->filter()->unique('id')->values();
                            $poData = is_string($order->po_number) && str_starts_with(trim($order->po_number), '{')
                                ? json_decode($order->po_number, true)
                                : [];
                            $statusClass = $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-700';
                            $statusLabel = $statusLabels[$order->status] ?? ucfirst($order->status);
                            $pickupSchedule = $order->pickup_date
                                ? \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') . ($order->pickup_time ? ' · ' . \Carbon\Carbon::parse($order->pickup_time)->format('H:i') : '')
                                : null;
                        @endphp

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-slate-50 to-white">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="space-y-3">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                                            <span class="text-sm text-gray-500">Pesanan #{{ $order->id }}</span>
                                            <span class="text-sm text-gray-400">{{ $order->created_at->format('d M Y · H:i') }}</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $order->user->company_name ?? $order->user->name }}</h3>
                                            <p class="text-sm text-gray-500">Kapal {{ $order->ship->name }} · {{ $order->items->count() }} item</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center justify-center rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 transition hover:bg-indigo-100">Lihat Detail</a>
                                        <a href="{{ route('admin.orders.total-sheet', $order) }}" class="inline-flex items-center justify-center rounded-lg border border-amber-100 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 transition hover:bg-amber-100">Hasil</a>
                                        <a href="{{ route('admin.orders.invoice', $order) }}" class="inline-flex items-center justify-center rounded-lg border border-emerald-100 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100">Download Invoice</a>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 grid gap-6 xl:grid-cols-[1.1fr_1fr_1fr]">
                                <div class="space-y-4">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="rounded-xl bg-slate-50 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Pesanan</p>
                                            <p class="mt-2 text-lg font-semibold text-slate-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="rounded-xl bg-slate-50 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Jadwal Pickup</p>
                                            <p class="mt-2 text-sm font-medium text-slate-700">{{ $pickupSchedule ?? 'Belum dijadwalkan' }}</p>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Lokasi Pickup</p>
                                            <p class="mt-1 text-sm text-gray-700">{{ $order->pickup_location ?: 'Belum diisi' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Catatan</p>
                                            <p class="mt-1 text-sm text-gray-700">{{ $order->notes ?: 'Tidak ada catatan tambahan.' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-semibold text-gray-900">Ringkasan Item</h4>
                                        <span class="text-xs text-gray-400">{{ $order->items->count() }} item</span>
                                    </div>
                                    <div class="mt-4 space-y-3">
                                        @foreach($order->items->take(4) as $item)
                                            <div class="rounded-xl border border-gray-100 p-4">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $item->product?->name ?? 'Produk tidak tersedia' }}</p>
                                                        <p class="text-xs text-gray-400">{{ $item->product?->vendor?->name ?? 'Tanpa vendor' }}</p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-sm font-semibold text-gray-900">{{ $formatQuantity($item->quantity) }} {{ $item->product?->unit ?? '' }}</p>
                                                        <p class="text-xs text-gray-400">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        @if($order->items->count() > 4)
                                            <div class="rounded-xl border border-dashed border-gray-200 p-4 text-sm text-gray-500">
                                                +{{ $order->items->count() - 4 }} item lainnya.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-semibold text-gray-900">Surat PO Vendor</h4>
                                        <span class="text-xs text-gray-400">{{ $vendors->count() }} vendor</span>
                                    </div>
                                    <div class="mt-4 space-y-3">
                                        @forelse($vendors as $vIdx => $vendor)
                                            @php
                                                $vendorSlug = \Illuminate\Support\Str::slug($vendor->name);
                                                $romans = ['01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'];
                                                $monthRoman = $romans[$order->created_at->format('m')];
                                                $year = $order->created_at->format('Y');
                                                $progressiveNumber = str_pad($order->id + $vIdx, 3, '0', STR_PAD_LEFT);
                                                $defaultPoNumber = "{$progressiveNumber}/AMS-PO-LBJ/{$monthRoman}/{$year}";
                                                $poNumber = $poData[$vendorSlug] ?? $defaultPoNumber;
                                            @endphp

                                            <div class="rounded-xl border border-gray-100 p-4">
                                                <p class="text-sm font-medium text-gray-900">{{ $vendor->name }}</p>
                                                <p class="mt-1 text-xs font-semibold text-indigo-600 break-all">{{ $poNumber }}</p>
                                                <a href="{{ route('admin.orders.po.preview', [$order, $vendor]) }}" class="mt-3 inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-indigo-700">
                                                    Preview Surat PO
                                                </a>
                                            </div>
                                        @empty
                                            <div class="rounded-xl border border-dashed border-gray-200 p-4 text-sm text-gray-400">
                                                Belum ada vendor untuk dibuatkan surat PO.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- ── Delivery Orders dari Ransum (no_do sudah dibuat) ─────── --}}
            @if($ransumOrders->isNotEmpty())
            <div class="mt-8">
                <h3 class="text-base font-semibold text-gray-700 mb-3">{{ __('Delivery Orders (DO) & Purchase Orders (PO) – Ransum') }}</h3>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('No. DO') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('No. PO') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Kapal') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Voyage') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Vendor') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Total') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Tgl. Pengiriman') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($ransumOrders as $ransum)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $ransum->no_do }}</td>
                                    
                                    {{-- Kolom PO Number yang akan muncul jika sudah disave dari preview --}}
                                    <td class="px-6 py-4 text-sm font-bold text-indigo-600">
    @php
        $poJson = $ransum->po_number;
        $poData = (is_string($poJson) && str_starts_with(trim($poJson), '{')) ? json_decode($poJson, true) : [];
    @endphp
    
    @if(is_array($poData) && count($poData) > 0)
        <div class="flex flex-col gap-1">
            @foreach($poData as $vSlug => $poNum)
                <div class="text-xs" title="{{ strtoupper(str_replace('-', ' ', $vSlug)) }}">
                    <span class="text-gray-400 font-normal uppercase">{{ strtoupper(str_replace('-', ' ', $vSlug)) }}:</span> <br>
                    {{ $poNum }}
                </div>
            @endforeach
        </div>
    @elseif(!empty($poJson) && !is_array($poData))
        {{ $poJson }}
    @else
        <span class="text-gray-400 font-normal italic text-xs">Belum di-download</span>
    @endif
</td>
                                    
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $ransum->vessel_name ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $ransum->voyage ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $ransum->vendor_name ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold">
                                        @if($ransum->total_belanja_ransum)
                                            Rp {{ number_format($ransum->total_belanja_ransum, 0, ',', '.') }}
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $ransum->delivery_date ? \Carbon\Carbon::parse($ransum->delivery_date)->format('d M Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.ransum.total.preview', $ransum->id) }}"
                                               class="text-amber-600 hover:text-amber-800 text-sm font-medium">{{ __('Total') }}</a>
                                            <a href="{{ route('admin.ransum.po.preview', $ransum->id) }}"
                                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">{{ __('PO') }}</a>
                                            <a href="{{ route('admin.ransum.preview', $ransum->id) }}"
                                               class="text-gray-500 hover:text-gray-700 text-sm font-medium">{{ __('Detail') }}</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
