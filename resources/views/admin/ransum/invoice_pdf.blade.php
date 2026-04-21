<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        @page { 
            size: A4 portrait;
            margin: 40px 50px 100px 50px; 
        }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; line-height: 1.3; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .text-blue { color: #1e3a8b; }

        .header-title { font-size: 22px; font-weight: bold; margin-bottom: 5px; letter-spacing: 0.5px; }
        .header-addr { font-size: 9px; margin-bottom: 10px; }
        .line-blue { border-top: 2px solid #1e3a8b; margin: 0 0 15px 0; }

        .inv-title { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 25px; letter-spacing: 1px; }

        .meta-table { width: 100%; margin-bottom: 25px; }
        .meta-table td { vertical-align: top; }

        .bg-yellow { background-color: #ffc107; font-weight: bold; padding: 2px 4px; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
        .items-table th { font-weight: bold; text-align: left; }
        .items-table th:nth-child(1) { text-align: center; width: 5%; }
        .items-table th:nth-child(3) { text-align: center; width: 25%; }

        /* Menghilangkan border dalam agar terlihat menyatu */
        .border-none-bottom { border-bottom: none !important; }
        .border-none-top { border-top: none !important; }

        .amount-wrap { width: 100%; border: none; margin: 0; padding: 0; }
        .amount-wrap td { border: none !important; padding: 0 !important; }

        .payment-info { margin-top: 40px; float: left; width: 60%; }
        .signature { margin-top: 60px; float: right; width: 30%; text-align: center; }

        .footer { position: fixed; bottom: -60px; left: 0; right: 0; font-size: 9px; }
        .footer-line { border-top: 2px solid #1e3a8b; margin-bottom: 8px; }
        .footer-table td { vertical-align: top; padding-bottom: 2px; }
    </style>
</head>
<body>

    {{-- Footer Bawah --}}
    <div class="footer">
        <div class="footer-line"></div>
        <table class="footer-table" width="100%">
            <tr>
                <td width="15%">Branch Jakarta</td>
                <td width="2%">:</td>
                <td width="83%">Pergudangan INKOPAU, Jl. R. E Martadinata No. 100 Blok. B03, Kel. Tanjung Priok<br>Kec. Tanjung Priok, Jakarta Utara, Jakarta 14310</td>
            </tr>
            <tr>
                <td>Phone</td>
                <td>:</td>
                <td>+62 31-3292288</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>:</td>
                <td><a href="mailto:andalanmaritimsejahtera@gmail.com" style="color: #1e3a8b; text-decoration: none;">andalanmaritimsejahtera@gmail.com</a></td>
            </tr>
        </table>
    </div>

    {{-- Header --}}
    <div class="text-center">
        <div class="header-title text-blue">PT Andalan Maritim Sejahtera</div>
        <div class="header-addr">Aloon - Aloon Priok Nomor 27, Perak Barat, Krembangan, Kota Surabaya, Jawa Timur 60177</div>
    </div>
    <div class="line-blue"></div>

    <div class="inv-title">INVOICE</div>

    {{-- Info Perusahaan & Klien --}}
    <table class="meta-table">
        <tr>
            <td width="55%">
                <div class="bold" style="margin-bottom: 3px;">Andalan Maritim Sejahtera, PT</div>
                <div>Aloon-Aloon Priok No. 27</div>
                <div>Perak Barat, Krembangan</div>
                <div>Kota Surabaya, Provinsi Jawa Timur 60177</div>

                <div style="margin-top: 15px;">Bill To:</div>
                <div class="bold" style="margin-bottom: 3px;">PT. MERATUS LINE</div>
                <div style="font-size: 10px; line-height: 1.4;">JL ALOON-ALOON PRIOK NO.27 RT 006 RW 008, PERAK<br>BARAT, KREMBANGAN , KOTA SURABAYA, JAWA TIMUR</div>
            </td>
            <td width="45%">
                <table width="100%">
                    <tr>
                        <td class="text-right" width="45%">Invoice Number :</td>
                        <td width="55%"><span class="bg-yellow">{{ $invoiceNumber }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-right">Invoice Date :</td>
                        <td>{{ \Carbon\Carbon::parse($invoiceDate)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-right">Tanggal Pemesanan :</td>
                        <td>{{ $upload->tanggal_pemesanan ?? \Carbon\Carbon::parse($invoiceDate)->subDays(4)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-right">Tanggal Pengiriman :</td>
                        <td>{{ $upload->tanggal_pengiriman ?? \Carbon\Carbon::parse($invoiceDate)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-right">No. Surat Jalan/DO :</td>
                        <td>{{ $upload->no_do ?? '009/DO-AMS-LBJ/III/2026' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Tabel Utama (Data Kapal Saja) --}}
    <table class="items-table">
        <thead>
            <tr>
                <th>No</th>
                <th class="text-center">DESCRIPTION</th>
                <th>AMOUNT (IDR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center border-none-bottom">1</td>
                <td class="border-none-bottom">Pembelian Ransum Vessel &nbsp;{{ strtoupper($upload->vessel_name) }}</td>
                <td class="border-none-bottom">
                    <table class="amount-wrap">
                        <tr>
                            <td width="15%">Rp</td>
                            <td width="85%" class="text-right">{{ number_format($upload->total_belanja_ransum, 0, '.', ',') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="text-center border-none-bottom border-none-top">2</td>
                <td class="border-none-bottom border-none-top">No. PO * : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $upload->po_number ?? '000' }}</td>
                <td class="border-none-bottom border-none-top"></td>
            </tr>
            <tr>
                <td class="text-center border-none-bottom border-none-top">3</td>
                <td class="border-none-bottom border-none-top">Voy * : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $upload->voyage ?? '-' }}</td>
                <td class="border-none-bottom border-none-top"></td>
            </tr>
            <tr>
                <td class="text-center border-none-top" style="height: 120px;">4</td>
                <td class="border-none-top">Port * : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $upload->port_tujuan ?? '-' }}</td>
                <td class="border-none-top"></td>
            </tr>
            <tr>
                <td colspan="2" class="text-right bold">TOTAL :</td>
                <td class="bold">
                    <table class="amount-wrap">
                        <tr>
                            <td width="15%">Rp</td>
                            <td width="85%" class="text-right">{{ number_format($upload->total_belanja_ransum, 0, '.', ',') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            {{-- Bagian Says dimasukkan langsung ke dalam tabel agar styling CSS terbaca PDF --}}
            <tr>
                <td colspan="3" style="background-color: #ffc107; padding: 6px 8px; border: 1px solid #000;">
                    Says &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <i>{{ $teksTerbilang ?? '#ERROR!' }}</i>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Detail Pembayaran & TTD --}}
    <div>
        <div class="payment-info">
            Paid to * :<br>
            ANDALAN MARITIM SEJAHTERA, PT<br>
            Bank Mandiri<br>
            KCP Surabaya Tanjung Perak<br>
            A/C No * : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;140-05-8808889-9 &nbsp;IDR
        </div>
        
        <div class="signature">
            <br><br><br><br><br>
            <span style="text-decoration: underline;">Irwinsyah</span>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>