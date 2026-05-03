<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Order - {{ $upload->no_do }}</title>
    <style>
        @page { size: A4 portrait; margin: 30px; }
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.3; color: #000; }
        
        /* Header Title */
        .title-container { width: 100%; margin-bottom: 5px; font-weight: bold; font-size: 14px; }
        .title-left { text-align: left; }
        .title-right { text-align: right; }

        /* Table Standard */
        table { width: 100%; border-collapse: collapse; }
        .table-bordered { border: 1px solid #000; }
        .table-bordered th, .table-bordered td { border: 1px solid #000; padding: 4px; vertical-align: top; }
        
        /* Inner tables (no border) */
        .table-inner { border: none; }
        .table-inner th, .table-inner td { border: none; padding: 1px 2px; vertical-align: top; }

        /* Utilities */
        .bg-yellow { background-color: #ffff00; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        /* Footer */
        .footer-note { font-size: 10px; margin-top: 15px; }
        .footer-note ol { padding-left: 15px; margin-top: 5px; margin-bottom: 15px; }
        .signature-table td { text-align: center; font-weight: bold; border: none; padding-top: 20px; }
    </style>
</head>
<body>

    {{-- Title DO & Company --}}
    <table class="title-container">
        <tr>
            <td class="title-left">DELIVERY ORDER</td>
            <td class="title-right">PT ANDALAN MARITIM SEJAHTERA</td>
        </tr>
    </table>

    {{-- Header Info DO (PO, DO, Dates) --}}
    <table class="table-bordered" style="margin-bottom: 10px;">
        <tr>
            <td width="50%" style="padding: 5px;">
                <table class="table-inner">
                    <tr><td width="25%">PO No. *</td><td width="5%">:</td><td>{{ $upload->po_number ?? '000' }}</td></tr>
                    <tr><td>Request Date *</td><td>:</td><td>{{ \Carbon\Carbon::parse($upload->request_date)->format('d F Y') }}</td></tr>
                </table>
            </td>
            <td width="50%" style="padding: 5px;">
                <table class="table-inner">
                    <tr><td width="25%">D/O No. *</td><td width="5%">:</td><td>{{ $upload->no_do }}</td></tr>
                    <tr><td>Delivery date *</td><td>:</td><td>{{ \Carbon\Carbon::parse($upload->delivery_date)->format('d F Y') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Deliver From & To --}}
    <table class="table-bordered" style="margin-bottom: 10px;">
        <tr>
            <th width="50%" class="bg-yellow text-center" style="padding: 2px;">Deliver From</th>
            <th width="50%" class="bg-yellow text-center" style="padding: 2px;">Deliver To</th>
        </tr>
        <tr>
            <td style="padding: 5px;">
                <div class="font-bold" style="margin-bottom: 5px;">PT Andalan Maritim Sejahtera (WH JKT)</div>
                <table class="table-inner">
                    <tr><td width="20%">PIC *</td><td width="5%">:</td><td>IRWINSYAH</td></tr>
                    <tr><td>Address *</td><td>:</td><td>Pergudangan INKOPAU, Jl. RE<br>Martadinata No. 100 Blok. B03<br>Tanjung Priok, Jakarta Utara</td></tr>
                    <tr><td>Phone *</td><td>:</td><td>+62 857-8211-6756</td></tr>
                    <tr><td>Email *</td><td>:</td><td><a href="mailto:irwinsyah.razi16@gmail.com" style="color: blue;">irwinsyah.razi16@gmail.com</a></td></tr>
                </table>
            </td>
            <td style="padding: 5px;">
                <table class="table-inner">
                    <tr><td width="25%">Vessel *</td><td width="5%">:</td><td class="font-bold">{{ strtoupper($upload->vessel_name) }}</td></tr>
                    <tr><td>Port *</td><td>:</td><td>{{ $upload->port_tujuan }}</td></tr>
                    <tr><td>ETB JKT *</td><td>:</td><td>{{ $upload->etb_jkt ?? '-' }}</td></tr>
                    <tr><td>Voy *</td><td>:</td><td>{{ $upload->voyage ?? '-' }}</td></tr>
                    <tr><td>Captain *</td><td>:</td><td>{{ $upload->captain ?? '-' }}</td></tr>
                    <tr><td>2/O / Cheff *</td><td>:</td><td>{{ $upload->two_o_cheff ?? '-' }}</td></tr>
                    <tr><td>Phone *</td><td>:</td><td>{{ $upload->phone_deliver_to ?? '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Tabel Items --}}
    <table class="table-bordered">
        <thead>
            <tr class="bg-yellow text-center font-bold">
                <td width="5%">No.</td>
                <td width="30%">Item</td>
                <td width="30%">Description</td>
                <td width="10%">Qty</td>
                <td width="15%">UOM</td>
                <td width="10%">Remark</td>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @if(isset($grouped) && count($grouped) > 0)
                @foreach($grouped as $section => $sectionItems)
                <tr>
                    <td></td>
                    <td colspan="5" class="font-bold">{{ strtoupper($section) }}</td>
                </tr>
                @foreach($sectionItems as $item)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $item->items }}</td>
                    <td>{{ $item->merk_spec }}</td>
                    <td class="text-center">{{ is_numeric($item->qty) && strpos((string)$item->qty, '.') !== false ? number_format($item->qty, 2) : $item->qty }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>{{ $item->ket_remarks }}</td>
                </tr>
                @endforeach
                @endforeach
            @else
                <tr><td colspan="6" class="text-center">Tidak ada data item.</td></tr>
            @endif
        </tbody>
    </table>

    {{-- Footer & TTD --}}
    <div class="footer-note">
        <div class="font-bold">NOTE:</div>
        <ol>
            <li>Harap untuk segera melakukan pengecekan ransum pada saat serah terima di kapal.</li>
            <li>Cek jumlah ransum, kesesuaian ransum, dan kondisi ransum pada saat serah terima di kapal.</li>
            <li>Jika ada ransum yang rusak/reject/tidak sesuai dapat berkoordinasi dengan tim delivery untuk di cek terlebih dahulu.</li>
            <li>Kendala ransum yang rusak/reject/tidak sesuai dapat di retur/tukar langsung dengan mengembalikan ransum yang rusak/reject/tidak sesuai ke tim delivery setelah terkonfirmasi bahwa ransum tersebut rusak/reject/tidak sesuai dengan melampirkan bukti foto dan video.</li>
            <li>Batas waktu complain/retur ransum yang rusak/reject/tidak sesuai adalah 1x24 jam, atau pada saat kapal masih berada di Jakarta/Area Dermaga Pelabuhan Tanjung Priok.</li>
            <li>Segala bentuk handling dan penyimpanan di Kapal serta kerusakan ransum pada saat dalam perjalanan bukan menjadi tanggung jawab AMS.</li>
            <li>Kritik dan saran dapat menghubungi kontak PIC di atas.</li>
        </ol>
        <div class="text-center font-bold" style="margin-top: 10px;">-- SEMANGAT BERTUGAS, JAGA KESEHATAN DAN KESELAMATAN --</div>
    </div>

    <table class="signature-table">
        <tr>
            <td width="33%">Delivered by<br><br><br><br><br>( IRWINSYAH )</td>
            <td width="33%"></td>
            <td width="33%">Received by<br><br><br><br><br>( ............................... )</td>
        </tr>
    </table>

</body>
</html>