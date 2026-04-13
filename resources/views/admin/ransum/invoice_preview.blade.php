<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Preview Invoice') }}: {{ $upload->vessel_name ?? $upload->original_filename }}
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Invoice Options Form --}}
            <form method="POST" action="{{ route('admin.ransum.invoice.download', $upload->id) }}" id="invoice-form">
                @csrf

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-base font-semibold text-gray-700 mb-4">{{ __('Pengaturan Invoice') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">{{ __('Nomor Invoice') }}</label>
                            <input type="text" name="invoice_number" id="invoice_number"
                                   value="INV-{{ str_pad($upload->id, 6, '0', STR_PAD_LEFT) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                                   oninput="document.getElementById('preview-inv-number').textContent = this.value">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">{{ __('Tanggal Invoice') }}</label>
                            <input type="date" name="invoice_date" id="invoice_date"
                                   value="{{ now()->format('Y-m-d') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                                   oninput="updateInvoiceDate(this.value)">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">{{ __('Catatan Tambahan') }}</label>
                            <input type="text" name="notes" id="notes"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                                   oninput="document.getElementById('preview-notes').textContent = this.value || ''">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ __('Download PDF') }}
                        </button>
                    </div>
                </div>
            </form>

            {{-- Invoice Preview Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 print:shadow-none print:border-none" id="invoice-preview">

                {{-- Header --}}
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <div class="text-2xl font-bold text-indigo-700">{{ $upload->vendor_name ?? __('Ship Order') }}</div>
                        <div class="text-sm text-gray-500 mt-1">{{ __('Bukti Permintaan Barang (BPB) Ransum') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-400 uppercase">{{ __('INVOICE') }}</div>
                        <div class="text-sm font-semibold text-gray-700 mt-1" id="preview-inv-number">INV-{{ str_pad($upload->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-sm text-gray-500" id="preview-inv-date">{{ now()->format('d F Y') }}</div>
                    </div>
                </div>

                <hr class="border-gray-200 mb-6">

                {{-- Info Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6 text-sm">
                    @if($upload->vessel_name)
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ __('Nama Kapal') }}</div>
                        <div class="font-semibold text-gray-800">{{ $upload->vessel_name }}</div>
                        @if($upload->vessel_code)<div class="text-xs text-gray-500">{{ $upload->vessel_code }}</div>@endif
                    </div>
                    @endif
                    @if($upload->voyage)
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ __('Voyage') }}</div>
                        <div class="font-semibold text-gray-800">{{ $upload->voyage }}</div>
                    </div>
                    @endif
                    @if($upload->date_start || $upload->date_end)
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ __('Periode') }}</div>
                        <div class="font-semibold text-gray-800">
                            {{ $upload->date_start ?? '' }}{{ ($upload->date_start && $upload->date_end) ? ' — ' : '' }}{{ $upload->date_end ?? '' }}
                        </div>
                    </div>
                    @endif
                    @if($upload->port_tujuan)
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ __('Port Tujuan') }}</div>
                        <div class="font-semibold text-gray-800">{{ $upload->port_tujuan }}</div>
                    </div>
                    @endif
                    @if($upload->jumlah_crew)
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ __('Jumlah Crew') }}</div>
                        <div class="font-semibold text-gray-800">{{ $upload->jumlah_crew }}</div>
                    </div>
                    @endif
                    @if($upload->jumlah_hari_pensupplaian)
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ __('Hari Pensupplaian') }}</div>
                        <div class="font-semibold text-gray-800">{{ $upload->jumlah_hari_pensupplaian }}</div>
                    </div>
                    @endif
                    @if($upload->contact_person)
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ __('Contact Person') }}</div>
                        <div class="font-semibold text-gray-800">{{ $upload->contact_person }}</div>
                    </div>
                    @endif
                    @if($upload->eta)
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ __('ETA') }}</div>
                        <div class="font-semibold text-gray-800">{{ $upload->eta }}</div>
                    </div>
                    @endif
                </div>

                {{-- Items Table --}}
                @foreach($grouped as $section => $items)
                <div class="mb-4">
                    <div class="bg-indigo-50 px-4 py-2 rounded-t-lg border border-indigo-100">
                        <span class="text-sm font-semibold text-indigo-800">{{ $section }}</span>
                    </div>
                    <div class="overflow-x-auto border border-t-0 border-gray-100 rounded-b-lg">
                        <table class="min-w-full text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">#</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Nama Ransum') }}</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Items / Merk') }}</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Supplier') }}</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Qty') }}</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Satuan') }}</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Non BKP') }}</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('BKP') }}</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('PPN 11%') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($items as $i => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 text-gray-400">{{ $i + 1 }}</td>
                                    <td class="px-3 py-2 text-gray-800 font-medium">{{ $item->nama_ransum ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-600">
                                        {{ $item->items ?? '' }}{{ ($item->items && $item->merk_spec) ? ' / ' : '' }}{{ $item->merk_spec ?? '' }}
                                        @if(!$item->items && !$item->merk_spec) - @endif
                                    </td>
                                    <td class="px-3 py-2 text-gray-600">{{ $item->supplier ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right text-gray-800">{{ $item->qty !== null ? number_format($item->qty, 0, ',', '.') : '-' }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $item->satuan ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600">{{ $item->harga !== null ? number_format($item->harga, 0, ',', '.') : '-' }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600">{{ $item->bkp !== null ? number_format($item->bkp, 0, ',', '.') : '-' }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600">{{ $item->ppn_11 !== null ? number_format($item->ppn_11, 0, ',', '.') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach

                {{-- Totals --}}
                <div class="flex justify-end mt-4 mb-6">
                    <div class="w-full max-w-sm">
                        <table class="w-full text-sm">
                            @if($upload->barang_non_bkp)
                            <tr>
                                <td class="py-1 text-gray-600">{{ __('Barang Non BKP') }}</td>
                                <td class="py-1 text-right text-gray-800">Rp {{ number_format($upload->barang_non_bkp, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($upload->barang_bkp)
                            <tr>
                                <td class="py-1 text-gray-600">{{ __('Barang BKP') }}</td>
                                <td class="py-1 text-right text-gray-800">Rp {{ number_format($upload->barang_bkp, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($upload->pajak_11)
                            <tr>
                                <td class="py-1 text-gray-600">{{ __('Pajak 11%') }}</td>
                                <td class="py-1 text-right text-gray-800">Rp {{ number_format($upload->pajak_11, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($upload->total_belanja_ransum)
                            <tr class="border-t border-gray-300">
                                <td class="pt-2 font-bold text-gray-800">{{ __('Total Belanja Ransum') }}</td>
                                <td class="pt-2 text-right font-bold text-indigo-700 text-base">Rp {{ number_format($upload->total_belanja_ransum, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($upload->budget)
                            <tr>
                                <td class="py-1 text-gray-600">{{ __('Budget') }}</td>
                                <td class="py-1 text-right text-gray-800">Rp {{ number_format($upload->budget, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($upload->selisih_anggaran !== null && $upload->selisih_anggaran != 0)
                            <tr>
                                <td class="py-1 text-gray-600">{{ __('Selisih Anggaran') }}</td>
                                <td class="py-1 text-right {{ $upload->selisih_anggaran < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    Rp {{ number_format($upload->selisih_anggaran, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Additional Notes --}}
                <div id="preview-notes-wrap" class="mb-6 hidden">
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-700">
                        <span class="font-semibold text-xs text-gray-500 uppercase">{{ __('Catatan:') }}</span>
                        <span id="preview-notes" class="ml-2"></span>
                    </div>
                </div>

                {{-- Signatures --}}
                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-2 gap-12 text-center text-sm">
                        <div class="flex flex-col items-center gap-2">
                            <span class="font-semibold text-gray-700">{{ __('Pemohon') }}</span>
                            @if($upload->pemohon_photo)
                                <img src="{{ route('admin.ransum.photo.serve', [$upload->id, 'pemohon']) }}"
                                     alt="TTD Pemohon"
                                     class="w-28 h-20 object-contain border border-gray-200 rounded bg-gray-50">
                            @else
                                <div class="w-28 h-20 border border-dashed border-gray-300 rounded bg-gray-50"></div>
                            @endif
                            <div class="border-t border-gray-400 pt-1 w-32 text-xs text-gray-700">{{ $upload->pemohon ?? '________________' }}</div>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <span class="font-semibold text-gray-700">{{ __('Menyetujui') }}</span>
                            @if($upload->menyetujui_photo)
                                <img src="{{ route('admin.ransum.photo.serve', [$upload->id, 'menyetujui']) }}"
                                     alt="TTD Menyetujui"
                                     class="w-28 h-20 object-contain border border-gray-200 rounded bg-gray-50">
                            @else
                                <div class="w-28 h-20 border border-dashed border-gray-300 rounded bg-gray-50"></div>
                            @endif
                            <div class="border-t border-gray-400 pt-1 w-32 text-xs text-gray-700">{{ $upload->menyetujui ?? '________________' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="mt-8 pt-4 border-t border-gray-100 text-center text-xs text-gray-400">
                    <p>{{ __('Dokumen ini dihasilkan secara otomatis oleh Sistem Manajemen BPB Ransum') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateInvoiceDate(val) {
            if (!val) return;
            const d = new Date(val + 'T00:00:00');
            const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            document.getElementById('preview-inv-date').textContent =
                d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }

        // Show/hide notes preview
        document.getElementById('notes').addEventListener('input', function () {
            const wrap = document.getElementById('preview-notes-wrap');
            wrap.classList.toggle('hidden', !this.value.trim());
        });
    </script>
</x-app-layout>
