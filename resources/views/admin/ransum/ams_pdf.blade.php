<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat AMS - {{ $upload->vessel_name }}</title>
    <style>
        @page { size: A4 portrait; margin: 20px 25px 25px 25px; }
        body { font-family: Arial, sans-serif; font-size: 13px; line-height: 1.3; color: #000; }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .bold        { font-weight: bold; }

        /* Green company header */
        .header-green {
            background-color: #fff;
            color: #000;
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            padding: 7px 4px;
        }

        /* Purple title */
        .header-purple {
            background-color: #fff;
            color: #000;
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            padding: 5px 4px;
            margin-bottom: 8px;
        }

        /* Vessel info table */
        .vessel-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .vessel-table td { padding: 1px 2px; vertical-align: top; }

        /* Main info layout */
        .main-row { width: 100%; border-collapse: collapse; }
        .main-row > tbody > tr > td { vertical-align: top; }

        /* Budget table */
        .budget-table { width: 100%; border-collapse: collapse; }
        .budget-table td { padding: 2px 2px; vertical-align: top; }

        /* Date / fill-in box */
        .fill-box {
            border: 2px solid #000;
            padding: 6px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 6px;
        }
        .date-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .date-table td { border: 1px solid #000; text-align: center; padding: 3px; }
        .over-budget {
            background-color: #000;
            color: #fff;
            font-weight: bold;
            padding: 4px 6px;
            margin-top: 6px;
            font-size: 12px;
        }

        /* Signature */
        .sig-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .sig-table td { padding: 2px; vertical-align: bottom; }

        /* Tagihan purple section */
        .tagihan-header {
            background-color: #fff;
            border: 1px solid #000;
            border-bottom: none;
            color: #000;
            font-weight: bold;
            padding: 5px 6px;
            margin-top: 10px;
            font-size: 12px;
        }
        .tagihan-body {
            background-color: #fff;
            border: 1px solid #000;
            border-top: none;
            padding: 4px 6px 6px 6px;
        }
        .tagihan-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .tagihan-table td { padding: 2px 3px; vertical-align: top; }

        /* Tanda terima */
        .tanda-header {
            background-color: #adadadff;
            color: #000;
            text-align: center;
            font-weight: bold;
            padding: 5px;
            margin-top: 8px;
            font-size: 12px;
            letter-spacing: 1px;
        }
        .tanda-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        .tanda-table td { padding: 5px 2px; vertical-align: bottom; }
        .dot-line { border-bottom: 1px dotted #000; min-width: 200px; }
    </style>
</head>
<body>

    {{-- Green company header --}}
    <div class="header-green">PT Andalan Maritim Sejahtera</div>

    {{-- Purple title --}}
    <div class="header-purple">PROVISION REQUEST LIST</div>

    {{-- Vessel Info --}}
    <table class="vessel-table">
        <tr>
            <td width="28%" class="bold">NAMA KAPAL/VESSEL NAME</td>
            <td width="2%">:</td>
            <td width="28%">{{ $upload->vessel_name ?? '' }}</td>
            <td width="8%" class="bold">ETA</td>
            <td width="2%">:</td>
            <td>{{ $upload->eta ?? '' }}</td>
        </tr>
        <tr>
            <td class="bold">VOYAGE</td>
            <td>:</td>
            <td>{{ $upload->voyage ?? '' }}</td>
            <td class="bold">PORT</td>
            <td>:</td>
            <td>{{ $upload->port_tujuan ?? '' }}</td>
        </tr>
        <tr>
            <td class="bold">RUTE</td>
            <td>:</td>
            <td colspan="4">{{ $upload->rute_sekarang ?? $upload->vessel_route ?? '' }}</td>
        </tr>
    </table>

    {{-- Main two-column layout: Budget (left) + Fill/Date box (right) --}}
    <table class="main-row" style="margin-bottom: 4px;">
        <tr>
            <td width="65%" style="padding-right: 8px;">

                <table class="budget-table">
                    <tr>
                        <td colspan="2" class="bold">ANGGARAN</td>
                        <td class="bold" style="color:#000;">BUDGET</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="20px">A</td>
                        <td class="bold">Anggaran per hari/per orang</td>
                        <td style="color:#000; font-size:11px;">Budget per day/per person</td>
                        <td></td>
                        <td class="text-right">
                            {{ $budgetPerOrang !== null ? number_format($budgetPerOrang, 0, '.', ',') : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td>B</td>
                        <td class="bold">Jumlah crew</td>
                        <td style="color:#000; font-size:11px;">Crew number</td>
                        <td></td>
                        <td class="text-right">{{ $upload->jumlah_crew ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>C</td>
                        <td class="bold">Jumlah hari pensupplaian</td>
                        <td style="color:#000; font-size:11px;">Total days to be supplied</td>
                        <td></td>
                        <td class="text-right">{{ $upload->jumlah_hari_pensupplaian ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>D</td>
                        <td class="bold">Total Anggaran ransum (Rupiah)</td>
                        <td style="color:#000; font-size:11px;">Total Budget</td>
                        <td></td>
                        <td class="text-right">{{ $upload->budget ? number_format($upload->budget, 0, '.', ',') : '' }}</td>
                    </tr>
                    <tr><td colspan="5" style="padding: 3px 0;"></td></tr>
                    <tr>
                        <td>E</td>
                        <td class="bold">Pembelanjaan</td>
                        <td></td><td></td><td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="padding-left:10px;">Barang Non BKP</td>
                        <td></td><td></td>
                        <td class="text-right">{{ number_format($upload->barang_non_bkp ?? 0, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="padding-left:10px;">Barang BKP</td>
                        <td></td><td></td>
                        <td class="text-right">{{ number_format($upload->barang_bkp ?? 0, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="padding-left:10px;">Pajak 11%</td>
                        <td></td><td></td>
                        <td class="text-right">{{ number_format($upload->pajak_11 ?? 0, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="bold text-center" style="padding-top:3px;">Total Belanja</td>
                        <td class="bold text-center" style="color:#000; font-size:11px; padding-top:3px;">Total Purchase</td>
                        <td></td>
                        <td class="text-right bold" style="padding-top:3px;">{{ number_format($upload->total_belanja_ransum ?? 0, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td>F</td>
                        <td class="bold">Selisih Anggaran &amp;<br>Pembelanjaan Ransum A-E</td>
                        <td style="color:#000; font-size:11px;">Balance of Budget<br>and Purchase A-E</td>
                        <td></td>
                        <td class="text-right">{{ $upload->selisih_anggaran !== null ? number_format($upload->selisih_anggaran, 0, '.', ',') : '' }}</td>
                    </tr>
                </table>

            </td>
            <td width="35%" style="vertical-align: top; padding-left: 4px;">
                <table class="date-table">
                    <tr>
                        <td class="bold">Start Date</td>
                        <td class="bold">End Date</td>
                    </tr>
                    <tr>
                        <td>{{ !empty($upload->date_start) ? \Carbon\Carbon::parse($upload->date_start)->format('d-M-Y') : '' }}</td>
                        <td>{{ !empty($upload->date_end) ? \Carbon\Carbon::parse($upload->date_end)->format('d-M-Y') : '' }}</td>
                    </tr>
                    <tr>
                        <td style="color:#000; font-size:12px;">{{ !empty($upload->date_start) ? \Carbon\Carbon::parse($upload->date_start)->format('m/d/Y') : 'MM/DD/YYYY' }}</td>
                        <td style="color:#000; font-size:12px;">{{ !empty($upload->date_end) ? \Carbon\Carbon::parse($upload->date_end)->format('m/d/Y') : 'MM/DD/YYYY' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Signatures --}}
    <table class="sig-table">
        <tr>
            <td width="50%" style="padding-top: 6px;">
                Pemohon: <br><br><br>
                <span style="border-bottom:1px solid #000; display:inline-block; width:140px;">&nbsp;</span><br><br>
                .....................................<br>
                <b>&nbsp;&nbsp;{{ $upload->pemohon ?? '' }}</b>
            </td>
            <td width="50%" style="padding-top: 6px;">
                Menyetujui: <br><br><br>
                <span style="border-bottom:1px solid #000; display:inline-block; width:140px;">&nbsp;</span><br><br>
                .....................................<br>
                <b>&nbsp;&nbsp;{{ $upload->menyetujui ?? '' }}</b>
            </td>
        </tr>
    </table>

    {{-- TAGIHAN / INVOICE section --}}
    <div class="tagihan-header">TAGIHAN/INVOICE (Diperiksa dan diisi oleh Crew Welfare/ Completed by Crew Welfare)</div>
    <div class="tagihan-body">
        <table class="tagihan-table">
            <tr>
                <td width="25%">Total Belanja</td>
                <td width="25%" style="color:#000;">Total Purchase</td>
                <td width="25%">{{ number_format($upload->total_belanja_ransum ?? 0, 0, '.', ',') }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Biaya Lembur</td>
                <td style="color:#000;">Over time charge</td>
                <td>{{ $biayaLembur > 0 ? number_format($biayaLembur, 0, '.', ',') : '' }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Sewa Perahu</td>
                <td style="color:#000;">Boat fee</td>
                <td>{{ $sewaPerahu > 0 ? number_format($sewaPerahu, 0, '.', ',') : '' }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="bold">Total Invoice</td>
                <td></td>
                <td class="bold">{{ number_format($totalInvoice, 0, '.', ',') }}</td>
                <td></td>
            </tr>
        </table>
    </div>

    {{-- TANDA TERIMA section --}}
    <div class="tanda-header">TANDA TERIMA RANSUM DI ATAS KAPAL</div>
    <table class="tanda-table">
        <tr>
            <td width="12%" class="bold">TANGGAL</td>
            <td width="3%">:</td>
            <td class="dot-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td width="40%"></td>
        </tr>
        <tr>
            <td class="bold">PUKUL</td>
            <td>:</td>
            <td class="dot-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4" class="bold" style="padding-top: 8px;">Diterima oleh</td>
        </tr>
    </table>
    <br><br><br>
    <div>(.................................)</div>

    <div style="font-size: 10px; font-weight: bold; margin-top: 12px;">
        Note: Mohon tanda tangan di lengkapi stempel kapal
    </div>

</body>
</html>
