<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Order {{ $upload->no_do }}</title>
    <style>
        @page { size: A4 portrait; margin: 40px 50px; }
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.3; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .title { font-size: 18px; font-weight: bold; text-decoration: underline; text-align: center; margin-bottom: 20px; }
        .company-name { font-size: 14px; font-weight: bold; margin-bottom: 20px; }
        .meta-table { width: 100%; margin-bottom: 20px; font-size: 11px; }
        .meta-table td { vertical-align: top; padding: 2px 0; }
        .address-box { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .address-box td { border: 1px solid #000; padding: 8px; vertical-align: top; width: 50%; }
        .address-box .box-title { font-weight: bold; margin-bottom: 5px; text-decoration: underline; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 5px; }
        .items-table th { background-color: #f0f0f0; }
        .section-row { background-color: #e8e8e8; font-weight: bold; }
        .footer-notes { font-size: 10px; margin-top: 20px; }
        .footer-notes ol { margin: 5px 0 0 20px; padding: 0; }
        .signatures { width: 100%; margin-top: 30px; text-align: center; }
        .signatures td { width: 50%; }
    </style>
</head>
<body>

    <div class="title">DELIVERY ORDER</div>
    
    <table class="meta-table">
        <tr>
            <td width="60%">
                <div class="company-name">PT ANDALAN MARITIM SEJAHTERA</div>
            </td>
            <td width="40%">
                <table width="100%">
                    <tr><td>PO No. *</td><td>: {{ $upload->po_number }}</td></tr>
                    <tr><td>Request Date *</td><td>: {{ $upload->request_date ? \Carbon\Carbon::parse($upload->request_date)->format('d F Y') : '-' }}</td></tr>
                    <tr><td>D/O No. *</td><td>: {{ $upload->no_do }}</td></tr>
                    <tr><td>Delivery date</td><td>: {{ $upload->delivery_date ? \Carbon\Carbon::parse($upload->delivery_date)->format('d F Y') : '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="address-box">
        <tr>
            <td>
                <div class="box-title">Deliver From</div>
                <div>PT Andalan Maritim Sejahtera (WH JKT)</div>
                <table width="100%" style="margin-top: 5px;">
                    <tr><td width="20%">PIC *</td><td>: IRWINSYAH</td></tr>
                    <tr><td>Address *</td><td>: Pergudangan INKOPAU, Jl. RE Martadinata No. 100 Blok. B03 Tanjung Priok, Jakarta Utara</td></tr>
                    <tr><td>Phone *</td><td>: +62 857-8211-6756</td></tr>
                    <tr><td>Email *</td><td>: irwinsyah.razi16@gmail.com</td></tr>
                </table>
            </td>
            <td>
                <div class="box-title">Deliver To</div>
                <div>{{ $upload->deliver_to }}</div>
                <table width="100%" style="margin-top: 5px;">
                    <tr><td width="30%">Port *</td><td>: {{ $upload->port_tujuan ?? '-' }}</td></tr>
                    <tr><td>ETB JKT *</td><td>: {{ $upload->etb_jkt ?? '-' }}</td></tr>
                    <tr><td>Voy *</td><td>: {{ $upload->voyage ?? '-' }}</td></tr>
                    <tr><td>Captain *</td><td>: {{ $upload->captain ?? '-' }}</td></tr>
                    <tr><td>2/O/Cheff *</td><td>: {{ $upload->contact_person ?? '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">Item No.</th>
                <th width="45%">Description</th>
                <th width="10%">Qty</th>
                <th width="15%">UOM</th>
                <th width="25%">Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grouped as $sectionName => $items)
                <tr class="section-row">
                    <td colspan="5">{{ $sectionName }}</td>
                </tr>
                @foreach($items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->nama_ransum }} <br><small><i>{{ $item->merk_spec }}</i></small></td>
                        <td class="text-center">{{ number_format($item->qty, 2) }}</td>
                        <td class="text-center">{{ $item->satuan }}</td>
                        <td>{{ $item->ket_remarks }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer-notes">
        <b>NOTE:</b>
        <ol>
            <li>Harap untuk segera melakukan pengecekan ransum pada saat serah terima di kapal.</li>
            <li>Cek jumlah ransum, kesesuaian ransum, dan kondisi ransum pada saat serah terima di kapal.</li>
            <li>Jika ada ransum yang rusak/reject/tidak sesuai dapat berkoordinasi dengan tim delivery untuk di cek terlebih dahulu.</li>
            <li>Kendala ransum yang rusak/reject/tidak sesuai dapat di retur/tukar langsung dengan mengembalikan ransum ke tim delivery.</li>
            <li>Batas waktu complain/retur ransum adalah 1x24 jam.</li>
            <li>Segala bentuk handling dan penyimpanan di Kapal serta kerusakan ransum pada saat dalam perjalanan bukan menjadi tanggung jawab AMS.</li>
            <li>Kritik dan saran dapat menghubungi kontak PIC di atas.</li>
        </ol>
        <div class="bold text-center" style="margin-top: 10px;">-- SEMANGAT BERTUGAS, JAGA KESEHATAN DAN KESELAMATAN --</div>
    </div>

    <table class="signatures">
        <tr>
            <td>
                <div>Delivered by</div>
                <br><br><br><br>
                <div>( IRWINSYAH )</div>
            </td>
            <td>
                <div>Received by</div>
                <br><br><br><br>
                <div>( ......................................... )</div>
            </td>
        </tr>
    </table>

</body>
</html>