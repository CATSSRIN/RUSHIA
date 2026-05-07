<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order – {{ request('vendor_name') ?? $supplierName }}</title>
    <style>
        @page { size: A4 portrait; margin: 30px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #000; line-height: 1.3; }

        /* Master Table Class */
        .excel-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .excel-table th, .excel-table td { border: 1px solid #000; }
        
        /* Helper Classes */
        .bg-yellow { background-color: #fde047; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .p-1 { padding: 4px 6px; }
        .align-top { vertical-align: top; }
        .border-none { border: none !important; }
        .border-bottom { border-bottom: 1px solid #000 !important; }
        .border-right { border-right: 1px solid #000 !important; }
        
        /* Specific Inner Tables */
        .inner-table { width: 100%; border-collapse: collapse; border: none; }
        .inner-table td { border: none; padding: 4px 6px; }
    </style>
</head>
<body>

    @php
        // Mengambil data langsung dari request form saat submit, mengantisipasi jika controller tidak meneruskannya
        $val_po_number = request('po_number') ?? $formData['po_number'] ?? '';
        
        $po_date_raw = request('po_date') ?? $formData['po_date'] ?? null;
        $val_po_date = $po_date_raw ? \Carbon\Carbon::parse($po_date_raw)->format('d M Y') : now()->format('d M Y');
        
        $deliv_date_raw = request('delivery_date') ?? $formData['delivery_date'] ?? null;
        $val_deliv_date = $deliv_date_raw ? \Carbon\Carbon::parse($deliv_date_raw)->format('d M Y') : '';

        $val_vessel = request('vessel_name') ?? $formData['vessel_name'] ?? $upload->vessel_name ?? '';
        $val_etb = request('etb') ?? $formData['etb'] ?? '';

        $val_vendor_name = request('vendor_name') ?? $formData['vendor_name'] ?? $supplierName;
        $val_vendor_pic = request('vendor_contact_name') ?? $formData['vendor_contact_name'] ?? '';
        $val_vendor_address = request('vendor_address') ?? $formData['vendor_address'] ?? '';
        $val_vendor_phone = request('vendor_phone') ?? $formData['vendor_phone'] ?? '';
        $val_vendor_email = request('vendor_email') ?? $formData['vendor_email'] ?? '';

        $val_deliver_to = request('deliver_to') ?? $formData['deliver_to'] ?? 'PT Andalan Maritim Sejahtera';
        $val_ship_pic = request('ship_to_pic') ?? $formData['ship_to_pic'] ?? '';
        $val_ship_address = request('ship_to_address') ?? $formData['ship_to_address'] ?? '';
        $val_ship_phone = request('ship_to_phone') ?? $formData['ship_to_phone'] ?? '';
        $val_ship_email = request('ship_to_email') ?? $formData['ship_to_email'] ?? '';

        $val_notes = request('notes') ?? $formData['notes'] ?? '';
        $val_discount = request('discount') ?? $formData['discount'] ?? '';
        $val_vat = request('vat') ?? $formData['vat'] ?? '';
        $val_shipping = request('shipping') ?? $formData['shipping'] ?? '';
        $val_prepared_by = request('prepared_by') ?? $formData['prepared_by'] ?? '';
    @endphp

    {{-- Header Title --}}
    <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 5px;">
        <tr>
            <td style="width: 50%; font-size: 16px; font-weight: bold; border: none;">PURCHASE ORDER</td>
            <td style="width: 50%; font-size: 16px; font-weight: bold; border: none; padding-left: 20px;">PT ANDALAN MARITIM SEJAHTERA</td>
        </tr>
    </table>

    {{-- Info Header (PO No, Ves, dll) --}}
    <table class="excel-table">
        <tr>
            <td style="width: 50%; padding: 0; border: 1px solid #000; vertical-align: top;">
                <table class="inner-table">
                    <tr>
                        <td class="border-bottom border-right" style="width: 25%;">PO No.</td>
                        <td class="border-bottom" style="width: 75%;">: <strong>{{ $val_po_number ?: '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="border-bottom border-right">Request Date</td>
                        <td class="border-bottom">: {{ $val_po_date }}</td>
                    </tr>
                    <tr>
                        <td class="border-right">Delivery Date</td>
                        <td>: {{ $val_deliv_date ?: '-' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; padding: 0; border: 1px solid #000; vertical-align: top;">
                <table class="inner-table">
                    <tr>
                        <td class="border-bottom border-right" style="width: 20%;">Ves</td>
                        <td class="border-bottom" style="width: 80%;">: {{ $val_vessel ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="border-bottom border-right">ETB</td>
                        <td class="border-bottom">: {{ $val_etb ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="border-right" style="height: 22px;"></td>
                        <td></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Vendor & Ship To --}}
    <table class="excel-table">
        <tr>
            <td class="bg-yellow text-center" style="width: 50%; padding: 4px;">Vendor</td>
            <td class="bg-yellow text-center" style="width: 50%; padding: 4px;">Ship To</td>
        </tr>
        <tr>
            <td style="padding: 0; vertical-align: top;">
                <table class="inner-table">
                    <tr>
                        <td style="width: 25%;">Vendor Name</td>
                        <td style="width: 5%;">:</td>
                        <td style="width: 70%; font-weight: bold;">{{ $val_vendor_name ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td>PIC</td>
                        <td>:</td>
                        <td>{{ $val_vendor_pic ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="align-top">Address</td>
                        <td class="align-top">:</td>
                        <td>{!! nl2br(e($val_vendor_address ?: '-')) !!}</td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td>:</td>
                        <td>{{ $val_vendor_phone ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td>{{ $val_vendor_email ?: '-' }}</td>
                    </tr>
                </table>
            </td>
            <td style="padding: 0; vertical-align: top;">
                <table class="inner-table">
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #f9fafb;">
                            {{ $val_deliver_to ?: '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%;">PIC</td>
                        <td style="width: 5%;">:</td>
                        <td style="width: 70%;">{{ $val_ship_pic ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="align-top">Address</td>
                        <td class="align-top">:</td>
                        <td>{!! nl2br(e($val_ship_address ?: '-')) !!}</td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td>:</td>
                        <td>{{ $val_ship_phone ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td style="color: blue; text-decoration: underline;">{{ $val_ship_email ?: '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Items --}}
    <table class="excel-table">
        <thead>
            <tr class="bg-yellow">
                <th class="p-1" style="width: 3%;">N</th>
                <th class="p-1" style="width: 25%;">Item</th>
                <th class="p-1" style="width: 20%;">Description</th>
                <th class="p-1" style="width: 6%;">Qt</th>
                <th class="p-1" style="width: 8%;">UOM</th>
                <th class="p-1" style="width: 11%;">Unit Price</th>
                <th class="p-1" style="width: 12%;">Total Price</th>
                <th class="p-1" style="width: 15%;">Supplier</th>
            </tr>
        </thead>
        <tbody>
            @foreach($editedItems as $idx => $item)
            <tr>
                <td class="p-1 text-center">{{ $idx + 1 }}</td>
                <td class="p-1">{{ $item['nama_ransum'] }}</td>
                <td class="p-1">{{ $item['keterangan'] ?? '' }}</td>
                <td class="p-1 text-center">{{ number_format($item['qty'], 2, '.', '') }}</td>
                <td class="p-1 text-center">{{ $item['satuan'] }}</td>
                <td class="p-1 text-right">{{ number_format($item['harga'], 0, ',', '.') }}</td>
                <td class="p-1 text-right">{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                <td class="p-1 text-center">{{ $val_vendor_name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Summary & Notes --}}
    <table class="excel-table" style="margin-bottom: 25px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding: 0;">
                <table class="inner-table">
                    <tr>
                        <td class="bg-yellow text-center border-bottom" style="padding: 4px;">Note's and Instructions</td>
                    </tr>
                    <tr>
                        <td style="height: 80px; vertical-align: top; padding: 6px; background-color: #fefce8;">
                            {!! nl2br(e($val_notes)) !!}
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding: 0;">
                <table class="inner-table">
                    <tr>
                        <td class="border-bottom border-right" style="width: 40%; padding-left: 8px;">Sub Total</td>
                        <td class="border-bottom text-center" style="width: 5%;">:</td>
                        <td class="border-bottom text-right font-bold" style="width: 55%; padding-right: 8px;">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="border-bottom border-right" style="padding-left: 8px;">Discount %</td>
                        <td class="border-bottom text-center">:</td>
                        <td class="border-bottom text-right" style="padding-right: 8px;">{{ $val_discount ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="border-bottom border-right" style="padding-left: 8px;">VAT (11%)</td>
                        <td class="border-bottom text-center">:</td>
                        <td class="border-bottom text-right" style="padding-right: 8px;">{{ $val_vat ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="border-bottom border-right" style="padding-left: 8px;">Shipping</td>
                        <td class="border-bottom text-center">:</td>
                        <td class="border-bottom text-right" style="padding-right: 8px;">{{ $val_shipping ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="border-right font-bold" style="padding-left: 8px;">TOTAL</td>
                        <td class="text-center font-bold">:</td>
                        <td class="text-right font-bold" style="font-size: 14px; padding-right: 8px;">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Signatures --}}
    <table style="width: 100%; border: none; text-align: center;">
        <tr>
            <td style="width: 50%; font-weight: bold; border: none; padding-bottom: 60px;">Supplier</td>
            <td style="width: 50%; font-weight: bold; border: none; padding-bottom: 60px;">Procurement</td>
        </tr>
        <tr>
            <td style="border: none;">
                <div style="border-bottom: 1px solid #000; display: inline-block; min-width: 220px; padding-bottom: 2px;">
                    {{ $val_vendor_pic ?: '' }}
                </div>
            </td>
            <td style="border: none;">
                <div style="border-bottom: 1px solid #000; display: inline-block; min-width: 220px; padding-bottom: 2px;">
                    {{ $val_prepared_by ?: str_replace(' (WH JKT)', '', $val_deliver_to) }}
                </div>
            </td>
        </tr>
    </table>

</body>
</html>