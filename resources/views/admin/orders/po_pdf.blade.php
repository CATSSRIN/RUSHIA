<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order - {{ $order->id }} - {{ $vendor->name }}</title>
    <style>
        @page { size: A4 portrait; margin: 30px; }
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.4; color: #000; }

        table { width: 100%; border-collapse: collapse; }
        .table-bordered { border: 1px solid #000; }
        .table-bordered th, .table-bordered td { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }

        .table-inner { border: none; }
        .table-inner th, .table-inner td { border: none; padding: 1px 2px; vertical-align: top; }

        .bg-header { background-color: #ffff00; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .title-row td { font-weight: bold; font-size: 14px; }

        .signature-table td { text-align: center; font-weight: bold; border: none; padding-top: 30px; }

        tfoot td { font-weight: bold; }
    </style>
</head>
<body>

    {{-- Title --}}
    <table style="margin-bottom: 6px;">
        <tr class="title-row">
            <td style="text-align: left;">PURCHASE ORDER</td>
            <td style="text-align: right;">PT ANDALAN MARITIM SEJAHTERA</td>
        </tr>
    </table>

    {{-- PO Info Header --}}
    <table class="table-bordered" style="margin-bottom: 10px;">
        <tr>
            <td width="50%" style="padding: 5px;">
                <table class="table-inner">
                    <tr>
                        <td width="30%">PO No.</td>
                        <td width="5%">:</td>
                        <td>PO-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}-{{ str_pad($vendor->id, 3, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td>{{ now()->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Order #</td>
                        <td>:</td>
                        <td>#{{ $order->id }}</td>
                    </tr>
                </table>
            </td>
            <td width="50%" style="padding: 5px;">
                <table class="table-inner">
                    <tr>
                        <td width="30%">Kapal</td>
                        <td width="5%">:</td>
                        <td class="font-bold">{{ strtoupper($order->ship->name) }}</td>
                    </tr>
                    <tr>
                        <td>Perusahaan</td>
                        <td>:</td>
                        <td>{{ $order->user->company_name ?? $order->user->name }}</td>
                    </tr>
                    @if($order->pickup_date)
                    <tr>
                        <td>Tgl. Pengambilan</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($order->pickup_date)->format('d F Y') }}</td>
                    </tr>
                    @endif
                    @if($order->pickup_location)
                    <tr>
                        <td>Lokasi</td>
                        <td>:</td>
                        <td>{{ $order->pickup_location }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Vendor (Supplier) Info --}}
    <table class="table-bordered" style="margin-bottom: 10px;">
        <tr>
            <th class="bg-header text-center" style="padding: 3px;">Kepada (Vendor / Supplier)</th>
        </tr>
        <tr>
            <td style="padding: 5px;">
                <table class="table-inner">
                    <tr>
                        <td width="20%">Nama</td>
                        <td width="3%">:</td>
                        <td class="font-bold">{{ $vendor->name }}</td>
                    </tr>
                    @if($vendor->address)
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $vendor->address }}</td>
                    </tr>
                    @endif
                    @if($vendor->contact_name)
                    <tr>
                        <td>Kontak</td>
                        <td>:</td>
                        <td>{{ $vendor->contact_name }}</td>
                    </tr>
                    @endif
                    @if($vendor->phone)
                    <tr>
                        <td>Telepon</td>
                        <td>:</td>
                        <td>{{ $vendor->phone }}</td>
                    </tr>
                    @endif
                    @if($vendor->email)
                    <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td>{{ $vendor->email }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Items Table --}}
    <table class="table-bordered" style="margin-bottom: 10px;">
        <thead>
            <tr class="bg-header text-center font-bold">
                <td width="5%">No.</td>
                <td width="35%">Produk</td>
                <td width="10%">Satuan</td>
                <td width="10%">Qty</td>
                <td width="20%">Harga Satuan</td>
                <td width="20%">Subtotal</td>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                <td class="text-center">{{ $item->product->unit ?? '-' }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right font-bold">Total</td>
                <td class="text-right font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    @if($order->notes)
    <div style="margin-bottom: 15px; padding: 6px; border: 1px solid #ccc; background: #f9f9f9;">
        <strong>Catatan:</strong> {{ $order->notes }}
    </div>
    @endif

    {{-- Signatures --}}
    <table class="signature-table">
        <tr>
            <td width="33%">Dipesan oleh,<br><br><br><br><br>( PT Andalan Maritim Sejahtera )</td>
            <td width="33%"></td>
            <td width="33%">Disetujui oleh,<br><br><br><br><br>( {{ $vendor->name }} )</td>
        </tr>
    </table>

</body>
</html>
