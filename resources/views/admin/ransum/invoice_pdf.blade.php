<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1f2937; padding: 24px 28px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px; }
        .company { font-size: 18px; font-weight: bold; color: #4f46e5; }
        .company-sub { font-size: 9px; color: #6b7280; margin-top: 2px; }
        .inv-title { font-size: 24px; font-weight: bold; color: #9ca3af; text-align: right; letter-spacing: 2px; }
        .inv-meta { text-align: right; color: #6b7280; font-size: 9px; margin-top: 2px; }
        .inv-num { font-size: 11px; font-weight: bold; color: #374151; }

        .divider { border: none; border-top: 1.5px solid #e5e7eb; margin: 14px 0; }

        /* Info grid */
        .info-row { display: flex; flex-wrap: wrap; gap: 0; margin-bottom: 14px; }
        .info-cell { width: 25%; padding-right: 10px; margin-bottom: 6px; }
        .info-label { font-size: 7px; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.05em; margin-bottom: 2px; font-weight: bold; }
        .info-value { font-size: 10px; font-weight: 600; color: #1f2937; }
        .info-sub { font-size: 8px; color: #6b7280; }

        /* Section header */
        .section-header { background: #eef2ff; padding: 5px 10px; font-size: 9px; font-weight: bold; color: #3730a3; border-radius: 3px 3px 0 0; border: 1px solid #c7d2fe; margin-top: 10px; }

        /* Items table */
        table { width: 100%; border-collapse: collapse; font-size: 8px; }
        thead th {
            background: #f9fafb;
            padding: 5px 6px;
            text-align: left;
            font-size: 7px;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            border-top: 1px solid #e5e7eb;
            white-space: nowrap;
        }
        thead th.right { text-align: right; }
        tbody td { padding: 4px 6px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        tbody td.right { text-align: right; }
        tbody tr:nth-child(even) td { background: #fafafa; }

        /* Totals */
        .totals-wrap { display: flex; justify-content: flex-end; margin-top: 12px; margin-bottom: 12px; }
        .totals-table { width: 260px; font-size: 9px; }
        .totals-table td { padding: 3px 6px; }
        .totals-table td.label { color: #6b7280; text-align: right; }
        .totals-table td.value { text-align: right; color: #374151; font-weight: 600; }
        .total-main td { border-top: 1.5px solid #d1d5db; font-size: 11px; font-weight: bold; }
        .total-main td.label { color: #374151; }
        .total-main td.value { color: #4f46e5; }

        /* Notes */
        .notes-box { padding: 8px 10px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; font-size: 8px; color: #374151; margin-bottom: 14px; }
        .notes-box strong { font-size: 7px; text-transform: uppercase; color: #9ca3af; display: block; margin-bottom: 2px; }

        /* Signatures */
        .signatures { display: flex; justify-content: flex-end; margin-top: 20px; padding-top: 12px; border-top: 1px solid #e5e7eb; }
        .sig-grid { display: flex; gap: 48px; }
        .sig-block { text-align: center; width: 110px; }
        .sig-label { font-size: 8px; font-weight: bold; color: #374151; margin-bottom: 4px; }
        .sig-image { width: 90px; height: 60px; object-fit: contain; border: 1px solid #e5e7eb; display: block; margin: 0 auto 4px; }
        .sig-placeholder { width: 90px; height: 60px; border-bottom: 1px solid #9ca3af; display: block; margin: 0 auto 4px; }
        .sig-name { font-size: 8px; color: #374151; font-weight: 600; border-top: 1px solid #9ca3af; padding-top: 3px; }

        /* Footer */
        .footer { margin-top: 18px; padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 7px; color: #9ca3af; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="company">{{ $upload->vendor_name ?? 'Ship Order' }}</div>
            <div class="company-sub">{{ __('Bukti Permintaan Barang (BPB) Ransum') }}</div>
        </div>
        <div>
            <div class="inv-title">INVOICE</div>
            <div class="inv-meta inv-num">{{ $invoiceNumber }}</div>
            <div class="inv-meta">{{ \Carbon\Carbon::parse($invoiceDate)->format('d F Y') }}</div>
        </div>
    </div>

    <hr class="divider">

    {{-- Info Grid --}}
    <div class="info-row">
        @if($upload->vessel_name)
        <div class="info-cell">
            <div class="info-label">{{ __('Nama Kapal') }}</div>
            <div class="info-value">{{ $upload->vessel_name }}</div>
            @if($upload->vessel_code)<div class="info-sub">{{ $upload->vessel_code }}</div>@endif
        </div>
        @endif
        @if($upload->voyage)
        <div class="info-cell">
            <div class="info-label">{{ __('Voyage') }}</div>
            <div class="info-value">{{ $upload->voyage }}</div>
        </div>
        @endif
        @if($upload->date_start || $upload->date_end)
        <div class="info-cell">
            <div class="info-label">{{ __('Periode') }}</div>
            <div class="info-value">{{ $upload->date_start ?? '' }}{{ ($upload->date_start && $upload->date_end) ? ' – ' : '' }}{{ $upload->date_end ?? '' }}</div>
        </div>
        @endif
        @if($upload->port_tujuan)
        <div class="info-cell">
            <div class="info-label">{{ __('Port Tujuan') }}</div>
            <div class="info-value">{{ $upload->port_tujuan }}</div>
        </div>
        @endif
        @if($upload->jumlah_crew)
        <div class="info-cell">
            <div class="info-label">{{ __('Jumlah Crew') }}</div>
            <div class="info-value">{{ $upload->jumlah_crew }}</div>
        </div>
        @endif
        @if($upload->jumlah_hari_pensupplaian)
        <div class="info-cell">
            <div class="info-label">{{ __('Hari Pensupplaian') }}</div>
            <div class="info-value">{{ $upload->jumlah_hari_pensupplaian }}</div>
        </div>
        @endif
        @if($upload->contact_person)
        <div class="info-cell">
            <div class="info-label">{{ __('Contact Person') }}</div>
            <div class="info-value">{{ $upload->contact_person }}</div>
        </div>
        @endif
        @if($upload->eta)
        <div class="info-cell">
            <div class="info-label">{{ __('ETA') }}</div>
            <div class="info-value">{{ $upload->eta }}</div>
        </div>
        @endif
    </div>

    {{-- Items per Section --}}
    @foreach($grouped as $section => $items)
        <div class="section-header">{{ $section }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width:20px">#</th>
                    <th>{{ __('Nama Ransum') }}</th>
                    <th>{{ __('Items / Merk') }}</th>
                    <th>{{ __('Supplier') }}</th>
                    <th class="right">{{ __('Qty') }}</th>
                    <th>{{ __('Satuan') }}</th>
                    <th class="right">{{ __('Non BKP') }}</th>
                    <th class="right">{{ __('BKP') }}</th>
                    <th class="right">{{ __('PPN 11%') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:600">{{ $item->nama_ransum ?? '-' }}</td>
                    <td style="color:#6b7280">
                        {{ $item->items ?? '' }}{{ ($item->items && $item->merk_spec) ? ' / ' : '' }}{{ $item->merk_spec ?? '' }}
                        @if(!$item->items && !$item->merk_spec) - @endif
                    </td>
                    <td>{{ $item->supplier ?? '-' }}</td>
                    <td class="right">{{ $item->qty !== null ? number_format($item->qty, 0, ',', '.') : '-' }}</td>
                    <td>{{ $item->satuan ?? '-' }}</td>
                    <td class="right">{{ $item->harga !== null ? number_format($item->harga, 0, ',', '.') : '-' }}</td>
                    <td class="right">{{ $item->bkp !== null ? number_format($item->bkp, 0, ',', '.') : '-' }}</td>
                    <td class="right">{{ $item->ppn_11 !== null ? number_format($item->ppn_11, 0, ',', '.') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    {{-- Totals --}}
    <div class="totals-wrap">
        <table class="totals-table">
            @if($upload->barang_non_bkp)
            <tr>
                <td class="label">{{ __('Barang Non BKP') }}</td>
                <td class="value">Rp {{ number_format($upload->barang_non_bkp, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($upload->barang_bkp)
            <tr>
                <td class="label">{{ __('Barang BKP') }}</td>
                <td class="value">Rp {{ number_format($upload->barang_bkp, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($upload->pajak_11)
            <tr>
                <td class="label">{{ __('Pajak 11%') }}</td>
                <td class="value">Rp {{ number_format($upload->pajak_11, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($upload->total_belanja_ransum)
            <tr class="total-main">
                <td class="label">{{ __('Total Belanja Ransum') }}</td>
                <td class="value">Rp {{ number_format($upload->total_belanja_ransum, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($upload->budget)
            <tr>
                <td class="label">{{ __('Budget') }}</td>
                <td class="value">Rp {{ number_format($upload->budget, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($upload->selisih_anggaran !== null && $upload->selisih_anggaran != 0)
            <tr>
                <td class="label">{{ __('Selisih Anggaran') }}</td>
                <td class="value" style="color: {{ $upload->selisih_anggaran < 0 ? '#dc2626' : '#16a34a' }}">
                    Rp {{ number_format($upload->selisih_anggaran, 0, ',', '.') }}
                </td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Notes --}}
    @if($notes)
    <div class="notes-box">
        <strong>{{ __('Catatan:') }}</strong>
        {{ $notes }}
    </div>
    @endif

    {{-- Signatures --}}
    <div class="signatures">
        <div class="sig-grid">
            <div class="sig-block">
                <div class="sig-label">{{ __('Pemohon') }}</div>
                @if($upload->pemohon_photo)
                    @php
                        $uploadDir = storage_path('app/private/ransum_uploads');
                        $photoPath = $uploadDir . '/' . $upload->pemohon_photo;
                    @endphp
                    @if(file_exists($photoPath))
                        <img src="{{ $photoPath }}" class="sig-image" alt="TTD Pemohon">
                    @else
                        <span class="sig-placeholder"></span>
                    @endif
                @else
                    <span class="sig-placeholder"></span>
                @endif
                <div class="sig-name">{{ $upload->pemohon ?? '____________________' }}</div>
            </div>
            <div class="sig-block">
                <div class="sig-label">{{ __('Menyetujui') }}</div>
                @if($upload->menyetujui_photo)
                    @php
                        $uploadDir = storage_path('app/private/ransum_uploads');
                        $photoPath = $uploadDir . '/' . $upload->menyetujui_photo;
                    @endphp
                    @if(file_exists($photoPath))
                        <img src="{{ $photoPath }}" class="sig-image" alt="TTD Menyetujui">
                    @else
                        <span class="sig-placeholder"></span>
                    @endif
                @else
                    <span class="sig-placeholder"></span>
                @endif
                <div class="sig-name">{{ $upload->menyetujui ?? '____________________' }}</div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ __('Dokumen ini dihasilkan secara otomatis oleh Sistem Manajemen BPB Ransum') }} &bull; {{ __('Dicetak pada') }} {{ now()->format('d F Y H:i') }}</p>
    </div>

</body>
</html>
