<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Buat Surat AMS (Provision Request List)') }}: {{ $upload->vessel_name ?? $upload->original_filename }}
            </h2>
            <a href="{{ route('admin.ransum.preview', $upload->id) }}"
               class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Kembali ke Preview') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('admin.ransum.ams.download', $upload->id) }}" id="ams-form">
                @csrf
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-base font-semibold text-gray-700 mb-4">{{ __('Informasi Surat AMS') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">
                                {{ __('Nomor Referensi (Reff)') }}
                                <span class="normal-case text-gray-400 font-normal">— Kosongkan untuk menggunakan data tersimpan</span>
                            </label>
                            <input type="text" name="ams_reff" id="ams_reff"
                                   value="{{ $upload->ams_reff ?? ('AMS-' . str_pad($upload->id, 5, '0', STR_PAD_LEFT)) }}"
                                   placeholder="{{ 'AMS-' . str_pad($upload->id, 5, '0', STR_PAD_LEFT) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">
                                {{ __('Biaya Lembur (Over Time Charge)') }}
                            </label>
                            <input type="number" name="biaya_lembur" id="biaya_lembur" min="0" step="1"
                                   value="{{ $upload->biaya_lembur ?? 0 }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                                   oninput="updateTotalInvoice()">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">
                                {{ __('Sewa Perahu (Boat Fee)') }}
                            </label>
                            <input type="number" name="sewa_perahu" id="sewa_perahu" min="0" step="1"
                                   value="{{ $upload->sewa_perahu ?? 0 }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                                   oninput="updateTotalInvoice()">
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 text-sm">
                        <div class="grid grid-cols-2 gap-1">
                            <div class="text-gray-500">Total Belanja Ransum:</div>
                            <div class="font-medium text-right">Rp {{ number_format($upload->total_belanja_ransum ?? 0, 0, '.', ',') }}</div>
                            <div class="text-gray-500">Biaya Lembur:</div>
                            <div class="font-medium text-right" id="sum-lembur">Rp {{ number_format($upload->biaya_lembur ?? 0, 0, '.', ',') }}</div>
                            <div class="text-gray-500">Sewa Perahu:</div>
                            <div class="font-medium text-right" id="sum-perahu">Rp {{ number_format($upload->sewa_perahu ?? 0, 0, '.', ',') }}</div>
                            <div class="text-gray-700 font-semibold border-t border-gray-300 pt-1 mt-1">Total Invoice:</div>
                            <div class="font-semibold text-right border-t border-gray-300 pt-1 mt-1" id="sum-total">
                                Rp {{ number_format(($upload->total_belanja_ransum ?? 0) + ($upload->biaya_lembur ?? 0) + ($upload->sewa_perahu ?? 0), 0, '.', ',') }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ __('Download Surat AMS (PDF)') }}
                        </button>
                    </div>
                </div>
            </form>

            {{-- Preview Section --}}
            <div class="bg-white shadow-sm border border-gray-200 p-8 print:shadow-none print:border-none" style="font-family: Arial, sans-serif; font-size: 11px; color: #000;">

                {{-- Green Header --}}
                <div style="background-color: #00b050; color: #fff; text-align: center; font-size: 16px; font-weight: bold; padding: 8px; margin-bottom: 0;">
                    PT Andalan Maritim Sejahtera
                </div>

                {{-- Purple Title --}}
                <div style="background-color: #7030a0; color: #fff; text-align: center; font-size: 12px; font-weight: bold; padding: 6px; margin-bottom: 8px;">
                    PROVISION REQUEST LIST
                </div>

                {{-- Vessel Info --}}
                <table style="width:100%; border-collapse: collapse; margin-bottom: 6px; font-size: 11px;">
                    <tr>
                        <td style="width: 30%; font-weight: bold;">NAMA KAPAL/VESSEL NAME</td>
                        <td style="width: 5%;">:</td>
                        <td style="width: 30%;">{{ $upload->vessel_name ?? '#REF!' }}</td>
                        <td style="width: 10%; font-weight: bold;">ETA</td>
                        <td style="width: 5%;">:</td>
                        <td>{{ $upload->eta ?? '#REF!' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">VOYAGE</td>
                        <td>:</td>
                        <td>{{ $upload->voyage ?? '#REF!' }}</td>
                        <td style="font-weight: bold;">PORT</td>
                        <td>:</td>
                        <td>{{ $upload->port_tujuan ?? '#REF!' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">RUTE</td>
                        <td>:</td>
                        <td colspan="4">{{ $upload->rute_sekarang ?? $upload->vessel_route ?? '#REF!' }}</td>
                    </tr>
                </table>

                <div style="display: flex; gap: 12px; margin-bottom: 8px;">
                    <div style="flex: 1;">
                        {{-- Budget Section --}}
                        <table style="width:100%; border-collapse: collapse; font-size: 11px;">
                            <tr>
                                <td colspan="4" style="font-weight: bold; padding-bottom: 4px;">ANGGARAN</td>
                                <td colspan="2" style="font-weight: bold; color: #7030a0; padding-bottom: 4px;">BUDGET</td>
                            </tr>
                            <tr>
                                <td style="width:3%;">A</td>
                                <td style="width:35%; font-weight:bold;">Anggaran per hari/per orang</td>
                                <td style="width:25%; color:#7030a0;">Budget per day/per person</td>
                                <td style="width:5%;"></td>
                                <td style="width:12%;">
                                    @php
                                        $crew = (float)($upload->jumlah_crew ?? 0);
                                        $hari = (float)($upload->jumlah_hari_pensupplaian ?? 0);
                                        $totalBudget = (float)($upload->budget ?? 0);
                                        $budgetPerOrang = ($crew > 0 && $hari > 0 && $totalBudget > 0)
                                            ? $totalBudget / $crew / $hari : null;
                                    @endphp
                                    {{ $budgetPerOrang !== null ? number_format($budgetPerOrang, 0, '.', ',') : '#REF!' }}
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>B</td>
                                <td style="font-weight:bold;">Jumlah crew</td>
                                <td style="color:#7030a0;">Crew number</td>
                                <td></td>
                                <td>{{ $upload->jumlah_crew ?? '#REF!' }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>C</td>
                                <td style="font-weight:bold;">Jumlah hari pensupplaian</td>
                                <td style="color:#7030a0;">Total days to be supplied</td>
                                <td></td>
                                <td>{{ $upload->jumlah_hari_pensupplaian ?? '#REF!' }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>D</td>
                                <td style="font-weight:bold;">Total Anggaran ransum (Rupiah)</td>
                                <td style="color:#7030a0;">Total Budget</td>
                                <td></td>
                                <td>{{ $upload->budget ? number_format($upload->budget, 0, '.', ',') : '#REF!' }}</td>
                                <td></td>
                            </tr>
                            <tr><td colspan="6" style="padding-top:4px;"></td></tr>
                            <tr>
                                <td>E</td>
                                <td style="font-weight:bold;">Pembelanjaan</td>
                                <td></td><td></td><td></td><td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="padding-left:12px;">Barang Non BKP</td>
                                <td></td><td></td>
                                <td>{{ number_format($upload->barang_non_bkp ?? 0, 0, '.', ',') }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="padding-left:12px;">Barang BKP</td>
                                <td></td><td></td>
                                <td>{{ number_format($upload->barang_bkp ?? 0, 0, '.', ',') }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="padding-left:12px;">Pajak 11%</td>
                                <td></td><td></td>
                                <td>{{ number_format($upload->pajak_11 ?? 0, 0, '.', ',') }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="font-weight:bold; padding-top:4px;">Total Belanja</td>
                                <td style="font-weight:bold; color:#7030a0; padding-top:4px;">Total Purchase</td>
                                <td></td>
                                <td style="font-weight:bold; padding-top:4px;">{{ number_format($upload->total_belanja_ransum ?? 0, 0, '.', ',') }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>F</td>
                                <td style="font-weight:bold;">Selisih Anggaran &amp;<br>Pembelanjaan Ransum A-E</td>
                                <td style="color:#7030a0;">Balance of Budget and Purchase A-E</td>
                                <td></td>
                                <td>{{ $upload->selisih_anggaran !== null ? number_format($upload->selisih_anggaran, 0, '.', ',') : '#REF!' }}</td>
                                <td></td>
                            </tr>
                        </table>
                    </div>

                    <div style="min-width: 160px;">
                        {{-- Fill in the Blanks box --}}
                        <div style="border: 2px solid #000; padding: 8px; text-align: center; font-weight: bold; font-size: 11px; margin-bottom: 8px;">
                            FILL IN THE BLANKS !!!
                        </div>
                        {{-- Date Range --}}
                        <table style="width:100%; border-collapse:collapse; font-size:10px;">
                            <tr>
                                <td style="border:1px solid #000; text-align:center; font-weight:bold; padding:3px;">Start Date</td>
                                <td style="border:1px solid #000; text-align:center; font-weight:bold; padding:3px;">End Date</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #000; text-align:center; padding:3px;">{{ $upload->date_start ?? '#REF!' }}</td>
                                <td style="border:1px solid #000; text-align:center; padding:3px;">{{ $upload->date_end ?? '#REF!' }}</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #000; text-align:center; color:#888; padding:2px; font-size:9px;">MM/DD/YYYY</td>
                                <td style="border:1px solid #000; text-align:center; color:#888; padding:2px; font-size:9px;">MM/DD/YYYY</td>
                            </tr>
                        </table>
                        {{-- Over budget warning --}}
                        <div style="background-color:#ff0000; color:#fff; font-weight:bold; padding:5px 8px; margin-top:8px; font-size:10px;">
                            ➡ Tidak boleh over budget
                        </div>
                    </div>
                </div>

                {{-- Signatures --}}
                <table style="width:100%; margin-top:12px; font-size:11px;">
                    <tr>
                        <td style="width:50%;">
                            Pemohon: &nbsp;&nbsp; {{ $upload->pemohon ?? '' }}<br><br>
                            <div style="border-bottom: 1px solid #000; width: 140px; margin-bottom:4px;"></div>
                            .....................................<br>
                            <strong>(Nama 2/O)</strong>
                        </td>
                        <td style="width:50%;">
                            Menyetujui: &nbsp;&nbsp; {{ $upload->menyetujui ?? '' }}<br><br>
                            <div style="border-bottom: 1px solid #000; width: 140px; margin-bottom:4px;"></div>
                            .....................................<br>
                            <strong>(Nama Master)</strong>
                        </td>
                    </tr>
                </table>

                {{-- TAGIHAN/INVOICE Purple Section --}}
                <div style="background-color:#cc99ff; padding:8px; margin-top:12px; font-size:11px;">
                    <div style="font-weight:bold; margin-bottom:6px;">TAGIHAN/INVOICE (Diperiksa dan diisi oleh Crew Welfare/ Completed by Crew Welfare)</div>
                    <table style="width:100%; font-size:11px;">
                        <tr>
                            <td style="width:25%;">Total Belanja</td>
                            <td style="width:25%; color:#333;">Total Purchase</td>
                            <td style="width:25%;">{{ number_format($upload->total_belanja_ransum ?? 0, 0, '.', ',') }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Biaya Lembur</td>
                            <td style="color:#333;">Over time charge</td>
                            <td>{{ number_format($upload->biaya_lembur ?? 0, 0, '.', ',') }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sewa Perahu</td>
                            <td style="color:#333;">Boat fee</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold;">Total Invoice</td>
                            <td></td>
                            <td style="font-weight:bold;">
                                {{ number_format(($upload->total_belanja_ransum ?? 0) + ($upload->biaya_lembur ?? 0) + ($upload->sewa_perahu ?? 0), 0, '.', ',') }}
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </div>

                {{-- TANDA TERIMA Section --}}
                <div style="background-color:#d0d0d0; text-align:center; font-weight:bold; padding:5px; margin-top:8px; font-size:11px; letter-spacing:1px;">
                    TANDA TERIMA RANSUM DI ATAS KAPAL
                </div>
                <div style="padding:16px 0; font-size:11px;">
                    <table style="width:100%;">
                        <tr>
                            <td style="width:15%; font-weight:bold; padding:6px 0;">TANGGAL</td>
                            <td style="width:5%;">:</td>
                            <td style="border-bottom: 1px dotted #000; width:40%;">&nbsp;</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold; padding:6px 0;">PUKUL</td>
                            <td>:</td>
                            <td style="border-bottom: 1px dotted #000;">&nbsp;</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="font-weight:bold; padding-top:10px;">Diterima oleh</td>
                        </tr>
                    </table>
                    <br><br><br>
                    <div>(................................)</div>
                </div>

                <div style="font-size:10px; font-weight:bold; margin-top:8px;">
                    Note: Mohon tanda tangan di lengkapi stempel kapal
                </div>

            </div>
        </div>
    </div>

    <script>
        const totalBelanja = {{ (float)($upload->total_belanja_ransum ?? 0) }};

        function formatNumber(n) {
            return 'Rp ' + Math.round(n).toLocaleString('id-ID');
        }

        function updateTotalInvoice() {
            const lembur = parseFloat(document.getElementById('biaya_lembur').value) || 0;
            const perahu = parseFloat(document.getElementById('sewa_perahu').value) || 0;
            document.getElementById('sum-lembur').textContent = formatNumber(lembur);
            document.getElementById('sum-perahu').textContent = formatNumber(perahu);
            document.getElementById('sum-total').textContent = formatNumber(totalBelanja + lembur + perahu);
        }
    </script>
</x-app-layout>
