<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order - {{ $po_number ?? ('PO-' . str_pad($order->id, 5, '0', STR_PAD_LEFT)) }}</title>
    <style>
        @page { size: A4 portrait; margin: 28px 32px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #111827; line-height: 1.35; }

        .header-company { font-size: 17px; font-weight: bold; color: #1e3a5f; }
        .header-sub     { font-size: 10px; color: #6b7280; margin-top: 2px; }
        .po-title       { font-size: 20px; font-weight: bold; color: #374151; letter-spacing: 2px; }
        .po-number      { font-weight: 600; color: #1e3a5f; font-size: 12px; }

        table  { width: 100%; border-collapse: collapse; }
        .main-divider { border: none; border-top: 2px solid #1e3a5f; margin: 8px 0 12px; }

        .info-table td  { padding: 2px 0; vertical-align: top; }
        .info-label     { color: #6b7280; width: 38%; }
        .info-sep       { width: 5%; }

        .box-table      { border: 1px solid #d1d5db; margin-bottom: 12px; }
        .box-table th   { background: #f3f4f6; padding: 5px 8px; text-align: left;
                          font-size: 9px; text-transform: uppercase; letter-spacing: .04em;
                          color: #6b7280; border-bottom: 1px solid #d1d5db; }
        .box-table td   { padding: 5px 8px; vertical-align: top; }
        .box-half       { width: 50%; }
        .box-divider    { border-right: 1px solid #d1d5db; }

        .items-table thead th {
            background: #1e3a5f; color: #fff;
            padding: 7px 8px; font-size: 10px;
            border: 1px solid #1e3a5f;
        }
        .items-table tbody td {
            padding: 5px 8px; border: 1px solid #e5e7eb; vertical-align: top;
        }
        .items-table tfoot td {
            padding: 7px 8px; font-weight: bold; border: 1px solid #d1d5db;
        }
        .total-label { text-align: right; color: #374151; }
        .total-value { text-align: right; color: #1e3a5f; font-size: 13px; }

        .sig-table td { text-align: center; padding: 4px 8px; }
        .sig-line { border-top: 1px solid #9ca3af; margin-top: 40px; padding-top: 4px; font-size: 10px; color: #6b7280; }
        .footer { text-align: center; font-size: 9px; color: #9ca3af; margin-top: 10px; padding-top: 8px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>

    {{-- Header --}}
    <table style="margin-bottom:6px;">
        <tr>
            <td style="vertical-align:top;">
                <div class="header-company">PT ANDALAN MARITIM SEJAHTERA</div>
                <div class="header-sub">Ship Supply Management</div>
            </td>
            <td style="text-align:right; vertical-align:top;">
                <div class="po-title">PURCHASE ORDER</div>
                <div class="po-number" style="margin-top:3px;">No. {{ $po_number ?? ('PO-' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . '-' . strtoupper(\Illuminate\Support\Str::slug($vendor->name))) }}</div>
            </td>
        </tr>
    </table>

    <hr class="main-divider">

    {{-- Dates / Ship / Company --}}
    <table style="margin-bottom:12px;">
        <tr>
            <td style="width:50%; vertical-align:top; padding-right:16px;">
                <table class="info-table">
                    <tr>
                        <td class="info-label">Tanggal PO</td>
                        <td class="info-sep">:</td>
                        <td>{{ $po_date ? \Carbon\Carbon::parse($po_date)->format('d F Y') : now()->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal Pengiriman</td>
                        <td class="info-sep">:</td>
                        <td>{{ $delivery_date ? \Carbon\Carbon::parse($delivery_date)->format('d F Y') : '—' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:50%; vertical-align:top; padding-left:16px;">
                <table class="info-table">
                    <tr>
                        <td class="info-label">Kapal</td>
                        <td class="info-sep">:</td>
                        <td>{{ $ship_name ?? $order->ship->name }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Perusahaan</td>
                        <td class="info-sep">:</td>
                        <td>{{ $company_name ?? ($order->user->company_name ?? $order->user->name) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Vendor box --}}
    <table class="box-table" style="margin-bottom:12px;">
        <tr>
            <th colspan="2">Kepada Yth. (Vendor / Supplier)</th>
        </tr>
        <tr>
            <td class="box-half box-divider">
                <table class="info-table">
                    <tr>
                        <td class="info-label" style="font-size:10px;">Nama Vendor</td>
                        <td class="info-sep" style="font-size:10px;">:</td>
                        <td style="font-weight:bold;">{{ $vendor_name ?? $vendor->name }}</td>
                    </tr>
                    <tr>
                        <td class="info-label" style="font-size:10px;">Alamat</td>
                        <td class="info-sep" style="font-size:10px;">:</td>
                        <td>{{ $vendor_address ?? ($vendor->address ?? '—') }}</td>
                    </tr>
                </table>
            </td>
            <td class="box-half">
                <table class="info-table">
                    <tr>
                        <td class="info-label" style="font-size:10px;">Telepon</td>
                        <td class="info-sep" style="font-size:10px;">:</td>
                        <td>{{ $vendor_phone ?? ($vendor->phone ?? '—') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label" style="font-size:10px;">Email</td>
                        <td class="info-sep" style="font-size:10px;">:</td>
                        <td>{{ $vendor_email ?? ($vendor->email ?? '—') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Deliver to --}}
    <table class="info-table" style="margin-bottom:12px;">
        <tr>
            <td class="info-label" style="width:18%;">Kirimkan ke</td>
            <td class="info-sep">:</td>
            <td>{{ $deliver_to ?? ($order->pickup_location ?? strtoupper($order->ship->name)) }}</td>
        </tr>
    </table>

    {{-- Items table --}}
    <table class="items-table" style="margin-bottom:12px;">
        <thead>
            <tr>
                <th style="width:5%; text-align:center;">No.</th>
                <th>Nama Barang / Produk</th>
                <th style="width:8%; text-align:center;">Satuan</th>
                <th style="width:7%; text-align:center;">Qty</th>
                <th style="width:14%; text-align:right;">Harga Satuan (Rp)</th>
                <th style="width:14%; text-align:right;">Jumlah (Rp)</th>
                <th style="width:14%; text-align:left;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($items as $idx => $item)
            @php
                $sub = isset($item['subtotal']) ? (float)$item['subtotal'] : ((float)($item['unit_price'] ?? 0) * (float)($item['quantity'] ?? 0));
                $grandTotal += $sub;
            @endphp
            <tr>
                <td style="text-align:center; color:#6b7280;">{{ $idx + 1 }}</td>
                <td>{{ $item['name'] ?? '' }}</td>
                <td style="text-align:center;">{{ $item['unit'] ?? 'pcs' }}</td>
                <td style="text-align:center;">{{ $item['quantity'] ?? '' }}</td>
                <td style="text-align:right;">Rp {{ number_format($item['unit_price'] ?? 0, 0, ',', '.') }}</td>
                <td style="text-align:right;">Rp {{ number_format($sub, 0, ',', '.') }}</td>
                <td>{{ $item['notes'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#f9fafb;">
                <td colspan="5" class="total-label">TOTAL</td>
                <td class="total-value">Rp {{ number_format($total_price ?? $grandTotal, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- Notes --}}
    @if(!empty($notes))
    <div style="margin-bottom:14px; padding:8px 10px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:4px;">
        <span style="font-size:9px; text-transform:uppercase; color:#6b7280; font-weight:bold;">Catatan: </span>
        <span>{{ $notes }}</span>
    </div>
    @endif

    {{-- Signatures --}}
    <table class="sig-table" style="margin-top:20px;">
        <tr>
            <td style="width:33%;">
                <div style="font-size:10px; color:#6b7280; margin-bottom:4px;">Disiapkan oleh</div>
                <div style="font-weight:bold; font-size:11px;">{{ $prepared_by ?? '___________________' }}</div>
                <div class="sig-line">Tanda Tangan</div>
            </td>
            <td style="width:33%;">
                <div style="font-size:10px; color:#6b7280; margin-bottom:4px;">Disetujui oleh</div>
                <div style="font-weight:bold; font-size:11px;">{{ $approved_by ?? '___________________' }}</div>
                <div class="sig-line">Tanda Tangan</div>
            </td>
            <td style="width:33%;">
                <div style="font-size:10px; color:#6b7280; margin-bottom:4px;">Diterima oleh</div>
                <div style="font-weight:bold; font-size:11px;">___________________</div>
                <div class="sig-line">( Vendor )</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        PT Andalan Maritim Sejahtera &bull; Ship Supply Management &bull; Dicetak: {{ now()->format('d M Y H:i') }}
    </div>

</body>
</html>
