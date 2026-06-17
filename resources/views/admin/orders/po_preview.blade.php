<x-app-layout>
    @php
        $poJson = $order->po_number;
        $poArray = (is_string($poJson) && str_starts_with(trim($poJson), '{')) ? json_decode($poJson, true) : [];
        $vendorSlug = \Illuminate\Support\Str::slug($vendor->name);
        $savedPoNumber = $poArray[$vendorSlug] ?? null;
        if (!$savedPoNumber) {
            $savedPo = $order->pos->first(fn($p) => $p->vendor_id == $vendor->id);
            $savedPoNumber = $savedPo ? $savedPo->po_number : null;
        }
        $defaultPoNumber = "PO-" . str_pad($order->id, 5, '0', STR_PAD_LEFT) . "-" . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::slug($vendor->name));
        $displayPoNumber = $savedPoNumber ?? $defaultPoNumber;
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Surat PO – {{ $vendor->name }} &nbsp;<span class="text-gray-400 font-normal text-base">Order #{{ $order->id }}</span>
            </h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">← Kembali</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Instruction bar --}}
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Semua field pada dokumen dapat diedit langsung. Klik <strong>Download PDF</strong> untuk mengunduh surat PO ini.
            </div>

            @if(!empty($savedPoNumber))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2 text-sm shadow-sm">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Dokumen PO ini telah berhasil dibuat dan disimpan di sistem.</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.orders.po.download', [$order, $vendor]) }}" id="po-form">
                @csrf

                {{-- ── DOCUMENT PREVIEW ─────────────────────────────────────── --}}
                <div class="bg-white border border-gray-300 shadow-sm rounded-lg p-8 font-sans text-sm text-gray-900" id="po-document">

                    {{-- Header --}}
                    <table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
                        <tr>
                            <td style="vertical-align:top;">
                                <div style="font-size:18px; font-weight:bold; color:#1e3a5f;">PT ANDALAN MARITIM SEJAHTERA</div>
                                <div style="font-size:11px; color:#6b7280;">Ship Supply Management</div>
                            </td>
                            <td style="text-align:right; vertical-align:top;">
                                <div style="font-size:22px; font-weight:bold; color:#374151; letter-spacing:2px;">PURCHASE ORDER</div>
                                <div class="editable-wrap" style="margin-top:4px;">
                                    <span class="field-label">No. PO:</span>
                                    <input type="text" name="po_number"
                                           value="{{ $displayPoNumber }}"
                                           class="po-input text-right" style="width:200px; font-weight:600; color:#1e3a5f;">
                                </div>
                            </td>
                        </tr>
                    </table>

                    <hr style="border:none; border-top:2px solid #1e3a5f; margin:8px 0 12px;">

                    {{-- Dates row --}}
                    <table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
                        <tr>
                            <td style="width:50%; vertical-align:top; padding-right:16px;">
                                <table style="width:100%; border-collapse:collapse;">
                                    <tr>
                                        <td style="padding:2px 0; width:40%; color:#6b7280;">Tanggal PO</td>
                                        <td style="padding:2px; width:5%;">:</td>
                                        <td><input type="date" name="po_date" value="{{ now()->format('Y-m-d') }}" class="po-input" style="width:140px;"></td>
                                    </tr>
                                    <tr>
                                        <td style="padding:2px 0; color:#6b7280;">Tanggal Pengiriman</td>
                                        <td style="padding:2px;">:</td>
                                        <td><input type="date" name="delivery_date"
                                                   value="{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('Y-m-d') : now()->addDays(3)->format('Y-m-d') }}"
                                                   class="po-input" style="width:140px;"></td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width:50%; vertical-align:top; padding-left:16px;">
                                <table style="width:100%; border-collapse:collapse;">
                                    <tr>
                                        <td style="padding:2px 0; width:40%; color:#6b7280;">Kapal</td>
                                        <td style="padding:2px; width:5%;">:</td>
                                        <td><input type="text" name="ship_name" value="{{ $order->ship->name }}" class="po-input" style="width:160px;"></td>
                                    </tr>
                                    <tr>
                                        <td style="padding:2px 0; color:#6b7280;">Perusahaan</td>
                                        <td style="padding:2px;">:</td>
                                        <td><input type="text" name="company_name" value="{{ $order->user->company_name ?? $order->user->name }}" class="po-input" style="width:160px;"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    {{-- Vendor info box --}}
                    <table style="width:100%; border-collapse:collapse; border:1px solid #d1d5db; margin-bottom:12px;">
                        <tr>
                            <th colspan="2" style="background:#f3f4f6; padding:6px 10px; text-align:left; font-size:10px; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; border-bottom:1px solid #d1d5db;">
                                Kepada Yth. (Vendor / Supplier)
                            </th>
                        </tr>
                        <tr>
                            <td style="padding:8px 10px; width:50%; vertical-align:top; border-right:1px solid #d1d5db;">
                                <table style="width:100%; border-collapse:collapse;">
                                    <tr>
                                        <td style="padding:2px 0; width:35%; color:#6b7280; font-size:11px;">Nama Vendor</td>
                                        <td style="padding:2px; width:5%; font-size:11px;">:</td>
                                        <td><input type="text" name="vendor_name" value="{{ $vendor->name }}" class="po-input" style="width:100%; font-weight:600;"></td>
                                    </tr>
                                    <tr>
                                        <td style="padding:2px 0; color:#6b7280; font-size:11px;">Alamat</td>
                                        <td style="padding:2px; font-size:11px;">:</td>
                                        <td><input type="text" name="vendor_address" value="{{ $vendor->address ?? '-' }}" class="po-input" style="width:100%;"></td>
                                    </tr>
                                </table>
                            </td>
                            <td style="padding:8px 10px; width:50%; vertical-align:top;">
                                <table style="width:100%; border-collapse:collapse;">
                                    <tr>
                                        <td style="padding:2px 0; width:35%; color:#6b7280; font-size:11px;">Telepon</td>
                                        <td style="padding:2px; width:5%; font-size:11px;">:</td>
                                        <td><input type="text" name="vendor_phone" value="{{ $vendor->phone ?? '-' }}" class="po-input" style="width:100%;"></td>
                                    </tr>
                                    <tr>
                                        <td style="padding:2px 0; color:#6b7280; font-size:11px;">Email</td>
                                        <td style="padding:2px; font-size:11px;">:</td>
                                        <td><input type="text" name="vendor_email" value="{{ $vendor->email ?? '-' }}" class="po-input" style="width:100%;"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    {{-- Deliver To --}}
                    <table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
                        <tr>
                            <td style="width:30%; color:#6b7280;">Kirimkan ke</td>
                            <td style="width:5%;">:</td>
                            <td><input type="text" name="deliver_to"
                                       value="{{ $order->pickup_location ?? strtoupper($order->ship->name) }}"
                                       class="po-input" style="width:280px;"></td>
                        </tr>
                    </table>

                    {{-- Items table --}}
                    <table style="width:100%; border-collapse:collapse; border:1px solid #d1d5db; margin-bottom:12px;">
                        <thead>
                            <tr style="background:#1e3a5f; color:#fff;">
                                <th style="padding:8px 10px; text-align:left; font-size:11px; border:1px solid #1e3a5f;">No.</th>
                                <th style="padding:8px 10px; text-align:left; font-size:11px; border:1px solid #1e3a5f;">Nama Barang / Produk</th>
                                <th style="padding:8px 10px; text-align:center; font-size:11px; border:1px solid #1e3a5f;">Satuan</th>
                                <th style="padding:8px 10px; text-align:center; font-size:11px; border:1px solid #1e3a5f;">Qty</th>
                                <th style="padding:8px 10px; text-align:right; font-size:11px; border:1px solid #1e3a5f;">Harga Satuan (Rp)</th>
                                <th style="padding:8px 10px; text-align:right; font-size:11px; border:1px solid #1e3a5f;">Jumlah (Rp)</th>
                                <th style="padding:8px 10px; text-align:left; font-size:11px; border:1px solid #1e3a5f;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            @php $grandTotal = 0; @endphp
                            @foreach($items as $idx => $item)
                            @php
                                $subtotal = $item->unit_price * $item->quantity;
                                $grandTotal += $subtotal;
                            @endphp
                            <tr style="border-bottom:1px solid #e5e7eb;" class="item-row" data-index="{{ $idx }}">
                                <td style="padding:6px 10px; border:1px solid #e5e7eb; text-align:center; vertical-align:middle; color:#6b7280; font-size:11px;">{{ $idx + 1 }}</td>
                                <td style="padding:4px 6px; border:1px solid #e5e7eb; vertical-align:middle;">
                                    <input type="text" name="items[{{ $idx }}][name]" value="{{ $item->product->name }}" class="po-input w-full">
                                </td>
                                <td style="padding:4px 6px; border:1px solid #e5e7eb; text-align:center; vertical-align:middle;">
                                    <input type="text" name="items[{{ $idx }}][unit]" value="{{ $item->product->unit ?? 'pcs' }}" class="po-input text-center" style="width:60px;">
                                </td>
                                <td style="padding:4px 6px; border:1px solid #e5e7eb; text-align:center; vertical-align:middle;">
                                    <input type="number" name="items[{{ $idx }}][quantity]" value="{{ $item->quantity }}" min="1"
                                           class="po-input text-center item-qty" data-idx="{{ $idx }}" style="width:60px;">
                                </td>
                                <td style="padding:4px 6px; border:1px solid #e5e7eb; text-align:right; vertical-align:middle;">
                                    <input type="number" name="items[{{ $idx }}][unit_price]" value="{{ $item->unit_price }}" min="0" step="1"
                                           class="po-input text-right item-price" data-idx="{{ $idx }}" style="width:110px;">
                                </td>
                                <td style="padding:4px 6px; border:1px solid #e5e7eb; text-align:right; vertical-align:middle;">
                                    <input type="number" name="items[{{ $idx }}][subtotal]" value="{{ $subtotal }}"
                                           class="po-input text-right item-subtotal bg-gray-50" data-idx="{{ $idx }}" readonly style="width:110px;">
                                </td>
                                <td style="padding:4px 6px; border:1px solid #e5e7eb; vertical-align:middle;">
                                    <input type="text" name="items[{{ $idx }}][notes]" value="" class="po-input w-full" placeholder="—">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#f9fafb;">
                                <td colspan="5" style="padding:8px 10px; text-align:right; font-weight:600; border:1px solid #d1d5db; color:#374151;">TOTAL</td>
                                <td style="padding:8px 10px; text-align:right; font-weight:bold; color:#1e3a5f; border:1px solid #d1d5db;" id="grand-total-display">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </td>
                                <td style="border:1px solid #d1d5db;"></td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- Notes --}}
                    <table style="width:100%; border-collapse:collapse; margin-bottom:16px;">
                        <tr>
                            <td style="width:15%; color:#6b7280; vertical-align:top; padding-top:4px;">Catatan</td>
                            <td style="width:5%; vertical-align:top; padding-top:4px;">:</td>
                            <td>
                                <textarea name="notes" rows="2" class="po-input" style="width:100%; resize:vertical;">{{ $order->notes ?? '' }}</textarea>
                            </td>
                        </tr>
                    </table>

                    {{-- Signature --}}
                    <table style="width:100%; border-collapse:collapse; margin-top:24px;">
                        <tr>
                            <td style="width:33%; text-align:center; padding:4px;">
                                <div style="color:#6b7280; font-size:11px; margin-bottom:4px;">Disiapkan oleh</div>
                                <input type="text" name="prepared_by" value="" class="po-input text-center" style="width:160px;" placeholder="Nama & Jabatan">
                                <div style="border-top:1px solid #9ca3af; margin-top:40px; padding-top:4px; font-size:11px; color:#6b7280;">Tanda Tangan</div>
                            </td>
                            <td style="width:33%; text-align:center; padding:4px;">
                                <div style="color:#6b7280; font-size:11px; margin-bottom:4px;">Disetujui oleh</div>
                                <input type="text" name="approved_by" value="" class="po-input text-center" style="width:160px;" placeholder="Nama & Jabatan">
                                <div style="border-top:1px solid #9ca3af; margin-top:40px; padding-top:4px; font-size:11px; color:#6b7280;">Tanda Tangan</div>
                            </td>
                            <td style="width:33%; text-align:center; padding:4px;">
                                <div style="color:#6b7280; font-size:11px; margin-bottom:4px;">Diterima oleh</div>
                                <div style="border-top:1px solid #9ca3af; margin-top:64px; padding-top:4px; font-size:11px; color:#6b7280;">( Vendor )</div>
                            </td>
                        </tr>
                    </table>

                    <hr style="border:none; border-top:1px solid #e5e7eb; margin:16px 0 8px;">
                    <div style="text-align:center; font-size:10px; color:#9ca3af;">
                        PT Andalan Maritim Sejahtera &bull; Ship Supply Management &bull; Dicetak: {{ now()->format('d M Y H:i') }}
                    </div>

                </div>
                {{-- END DOCUMENT --}}

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('admin.orders.index') }}"
                       class="px-5 py-2.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Styles for editable inputs inside the document preview --}}
    <style>
        .po-input {
            border: none;
            border-bottom: 1px dashed #9ca3af;
            background: transparent;
            outline: none;
            padding: 1px 4px;
            font-size: 12px;
            font-family: inherit;
            color: inherit;
            transition: background 0.15s;
        }
        .po-input:focus {
            background: #eff6ff;
            border-bottom-color: #3b82f6;
            border-radius: 2px;
        }
        .po-input.w-full { width: 100%; }
        .po-input.text-right { text-align: right; }
        .po-input.text-center { text-align: center; }
        .po-input.bg-gray-50 { background: #f9fafb; }
        textarea.po-input { border: 1px dashed #9ca3af; padding: 4px 6px; border-radius: 4px; }
        textarea.po-input:focus { border-color: #3b82f6; background: #eff6ff; }
    </style>

    <script>
        // Auto-update subtotal when qty or price changes
        document.querySelectorAll('.item-qty, .item-price').forEach(function(input) {
            input.addEventListener('input', function() {
                var idx = this.dataset.idx;
                var qty = parseFloat(document.querySelector('.item-qty[data-idx="' + idx + '"]').value) || 0;
                var price = parseFloat(document.querySelector('.item-price[data-idx="' + idx + '"]').value) || 0;
                var sub = qty * price;
                document.querySelector('.item-subtotal[data-idx="' + idx + '"]').value = sub;
                updateGrandTotal();
            });
        });

        function updateGrandTotal() {
            var total = 0;
            document.querySelectorAll('.item-subtotal').forEach(function(el) {
                total += parseFloat(el.value) || 0;
            });
            document.getElementById('grand-total-display').textContent = 'Rp ' + total.toLocaleString('id-ID', {minimumFractionDigits: 0});
        }

        // Auto-reload the page after submitting the PO form (since download keeps user on the same page)
        document.getElementById('po-form').addEventListener('submit', function() {
            setTimeout(function() {
                window.location.reload();
            }, 1000);
        });
    </script>
</x-app-layout>
