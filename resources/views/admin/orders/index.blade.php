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

            @php
                $ordersWithPos = $orders->filter(function($order) {
                    return $order->pos->isNotEmpty();
                });
            @endphp

            @if($ordersWithPos->isNotEmpty())
                <div class="space-y-3">
                    <h3 class="text-base font-semibold text-gray-700">{{ __('Purchase Orders (PO) – Pesanan') }}</h3>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Kapal / Pesanan') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Nomor PO') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Vendor') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Tgl. Dibuat') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Status PO') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($ordersWithPos as $order)
                                        @php $poCount = $order->pos->count(); @endphp
                                        @foreach($order->pos as $index => $po)
                                            <tr class="hover:bg-gray-50 transition">
                                                @if($index === 0)
                                                    <td class="px-6 py-4 text-sm font-medium text-gray-900" rowspan="{{ $poCount }}">
                                                        <div class="font-semibold text-indigo-600">
                                                            {{ $order->ship->name ?? 'Kapal Tidak Diketahui' }}
                                                        </div>
                                                        <div class="text-xs text-gray-400 mt-1">
                                                            Order #{{ $order->id }} · {{ $order->created_at->format('d M Y') }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-0.5">
                                                            {{ $order->user->company_name ?? $order->user->name }}
                                                        </div>
                                                    </td>
                                                @endif
                                                <td class="px-6 py-4 text-sm font-semibold">
                                                    <a href="{{ route('admin.orders.po.serve_saved', $po->id) }}" target="_blank" class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-900 hover:underline">
                                                        <span>{{ $po->po_number }}</span>
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                        </svg>
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-700">
                                                    {{ $po->vendor->name ?? 'Vendor Tidak Diketahui' }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    {{ $po->created_at->format('d M Y · H:i') }}
                                                </td>
                                                <td class="px-6 py-4 text-sm">
                                                    <form method="POST" action="{{ route('admin.orders.po.update_status', $po->id) }}" class="inline-flex gap-1.5">
                                                        @csrf
                                                        <button type="submit" name="status" value="menunggu" class="px-1.5 py-0.5 text-[9px] font-bold rounded transition border {{ $po->status === 'menunggu' ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' }}">Menunggu</button>
                                                        <button type="submit" name="status" value="diproses" class="px-1.5 py-0.5 text-[9px] font-bold rounded transition border {{ $po->status === 'diproses' ? 'bg-blue-100 text-blue-800 border-blue-300' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' }}">Diproses</button>
                                                        <button type="submit" name="status" value="selesai" class="px-1.5 py-0.5 text-[9px] font-bold rounded transition border {{ $po->status === 'selesai' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' }}">Selesai</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

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
                                                $savedPo = $order->pos->first(fn($p) => $p->vendor_id == $vendor->id);
                                            @endphp

                                            <div class="rounded-xl border border-gray-100 p-4 {{ $savedPo ? 'bg-slate-50/50' : '' }}">
                                                <p class="text-sm font-medium text-gray-900">{{ $vendor->name }}</p>
                                                @if($savedPo)
                                                    <div class="mt-2 flex flex-col gap-1.5">
                                                        <a href="{{ route('admin.orders.po.serve_saved', $savedPo->id) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline inline-flex items-center gap-1">
                                                            {{ $savedPo->po_number }}
                                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                        </a>
                                                        
                                                        <div class="flex items-center gap-1 mt-1">
                                                            <form method="POST" action="{{ route('admin.orders.po.update_status', $savedPo->id) }}" class="inline-flex gap-1">
                                                                @csrf
                                                                <button type="submit" name="status" value="menunggu" class="px-1.5 py-0.5 text-[9px] font-bold rounded transition border {{ $savedPo->status === 'menunggu' ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' }}">Menunggu</button>
                                                                <button type="submit" name="status" value="diproses" class="px-1.5 py-0.5 text-[9px] font-bold rounded transition border {{ $savedPo->status === 'diproses' ? 'bg-blue-100 text-blue-800 border-blue-300' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' }}">Diproses</button>
                                                                <button type="submit" name="status" value="selesai" class="px-1.5 py-0.5 text-[9px] font-bold rounded transition border {{ $savedPo->status === 'selesai' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' }}">Selesai</button>
                                                            </form>
                                                        </div>

                                                        <div class="mt-2 pt-2 border-t border-gray-200/60">
                                                            <a href="{{ route('admin.orders.po.preview', [$order, $vendor]) }}" class="text-[10px] text-gray-400 hover:text-indigo-600 transition flex items-center gap-1">
                                                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                Edit / Regenerate PO
                                                            </a>
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="mt-1 text-xs font-semibold text-indigo-600 break-all">{{ $poNumber }}</p>
                                                    <a href="{{ route('admin.orders.po.preview', [$order, $vendor]) }}" class="mt-3 inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-indigo-700">
                                                        Preview Surat PO
                                                    </a>
                                                @endif
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
            <div class="mt-8 space-y-3">
                <h3 class="text-base font-semibold text-gray-700 mb-3">{{ __('Delivery Orders (DO) & Purchase Orders (PO) – Ransum') }}</h3>
                <div class="space-y-5">
                    @foreach($ransumOrders as $ransum)
                        @php
                            $ransumStatusClass = $ransum->status === 'imported' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700';
                            $ransumStatusLabel = $ransum->status === 'imported' ? 'Imported' : ucfirst($ransum->status);
                            $ransumDeliverySchedule = $ransum->delivery_date
                                ? \Carbon\Carbon::parse($ransum->delivery_date)->format('d M Y')
                                : null;
                        @endphp

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-slate-50 to-white">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="space-y-3">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $ransumStatusClass }}">{{ $ransumStatusLabel }}</span>
                                            <span class="text-sm text-gray-500">DO #{{ $ransum->no_do }}</span>
                                            <span class="text-sm text-gray-400">{{ $ransum->created_at->format('d M Y · H:i') }}</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $ransum->vessel_name }}</h3>
                                            <p class="text-sm text-gray-500">Voyage {{ $ransum->voyage }} · {{ $ransum->items->count() }} item</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.ransum.preview', $ransum->id) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-100 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">{{ __('Detail') }}</a>
                                        <a href="{{ route('admin.ransum.total.preview', $ransum->id) }}" class="inline-flex items-center justify-center rounded-lg border border-amber-100 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 transition hover:bg-amber-100">{{ !empty($ransum->vessel_code) ? $ransum->vessel_code : 'MM1' }}</a>
                                        <a href="{{ route('admin.ransum.list.preview', $ransum->id) }}" class="inline-flex items-center justify-center rounded-lg border border-blue-100 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 transition hover:bg-blue-100">{{ __('List') }}</a>
                                        <a href="{{ route('admin.ransum.po.preview', $ransum->id) }}" class="inline-flex items-center justify-center rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 transition hover:bg-indigo-100">{{ __('PO') }}</a>
                                        <a href="{{ route('admin.ransum.invoice', $ransum->id) }}" class="inline-flex items-center justify-center rounded-lg border border-emerald-100 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100">Invoice</a>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 grid gap-6 xl:grid-cols-[1.1fr_1fr_1fr]">
                                <div class="space-y-4">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="rounded-xl bg-slate-50 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Belanja Ransum</p>
                                            <p class="mt-2 text-lg font-semibold text-slate-900">
                                                @if($ransum->total_belanja_ransum)
                                                    Rp {{ number_format($ransum->total_belanja_ransum, 0, ',', '.') }}
                                                @else
                                                    —
                                                @endif
                                            </p>
                                        </div>
                                        <div class="rounded-xl bg-slate-50 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Tanggal Pengiriman</p>
                                            <p class="mt-2 text-sm font-medium text-slate-700">{{ $ransumDeliverySchedule ?? 'Belum dijadwalkan' }}</p>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Lokasi Pengiriman / Deliver To</p>
                                            <p class="mt-1 text-sm text-gray-700">{{ $ransum->deliver_to ?: 'Belum diisi' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Captain</p>
                                            <p class="mt-1 text-sm text-gray-700">{{ $ransum->captain ?: '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-semibold text-gray-900">Ringkasan Item</h4>
                                        <span class="text-xs text-gray-400">{{ count($ransum->grouped_items_by_vendor ?? []) }} vendor</span>
                                    </div>
                                    <div class="mt-4 space-y-3">
                                        @foreach(($ransum->grouped_items_by_vendor ?? []) as $vendorName => $items)
                                            @php
                                                $vendorSlug = Illuminate\Support\Str::slug($vendorName);
                                                $hasPo = $ransum->pos->contains('supplier_key', $vendorSlug);
                                            @endphp
                                            <div class="rounded-xl border p-4 transition duration-200 {{ $hasPo ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-white border-gray-100 text-gray-700' }}">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div>
                                                        <p class="text-sm font-semibold {{ $hasPo ? 'text-emerald-900' : 'text-gray-900' }}">{{ $vendorName }}</p>
                                                        <p class="text-xs mt-0.5 {{ $hasPo ? 'text-emerald-600/90' : 'text-gray-400' }}">Supplier Ransum</p>
                                                    </div>
                                                    <div class="text-right">
                                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $hasPo ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                                            {{ count($items) }} item
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-semibold text-gray-900">Surat PO Vendor</h4>
                                        <span class="text-xs text-gray-400">{{ $ransum->pos->count() }} vendor</span>
                                    </div>
                                    <div class="mt-4 space-y-3">
                                        @forelse($ransum->pos as $po)
                                            <div class="rounded-xl border border-gray-100 p-4 bg-slate-50/50">
                                                <p class="text-sm font-medium text-gray-900">{{ $po->vendor_name }}</p>
                                                <div class="mt-2 flex flex-col gap-1.5">
                                                    <a href="{{ route('admin.ransum.po.serve_saved', $po->id) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline inline-flex items-center gap-1">
                                                        {{ $po->po_number }}
                                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                    </a>
                                                    
                                                    <div class="flex items-center gap-1 mt-1">
                                                        <form method="POST" action="{{ route('admin.ransum.po.update_status', $po->id) }}" class="inline-flex gap-1">
                                                            @csrf
                                                            <button type="submit" name="status" value="menunggu" class="px-1.5 py-0.5 text-[9px] font-bold rounded transition border {{ $po->status === 'menunggu' ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' }}">Menunggu</button>
                                                            <button type="submit" name="status" value="diproses" class="px-1.5 py-0.5 text-[9px] font-bold rounded transition border {{ $po->status === 'diproses' ? 'bg-blue-100 text-blue-800 border-blue-300' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' }}">Diproses</button>
                                                            <button type="submit" name="status" value="selesai" class="px-1.5 py-0.5 text-[9px] font-bold rounded transition border {{ $po->status === 'selesai' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' }}">Selesai</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="rounded-xl border border-dashed border-gray-200 p-4 text-sm text-gray-400">
                                                Belum ada PO yang dibuat.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
