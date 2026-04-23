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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('admin.ransum.invoice.download', $upload->id) }}" id="invoice-form">
                @csrf
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">{{ __('Nomor Invoice') }}</label>
                            <input type="text" name="invoice_number" id="invoice_number"
                                   value="INV-AMS-{{ str_pad($upload->id, 5, '0', STR_PAD_LEFT) }}"
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
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ __('Download PDF') }}
                        </button>
                    </div>
                </div>
            </form>

            <div class="bg-white shadow-sm border border-gray-200 p-10 print:shadow-none print:border-none" style="font-family: Arial, sans-serif; color: #000;">
                
                {{-- Header --}}
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-[#1e3a8b] mb-1 tracking-wide">PT Andalan Maritim Sejahtera</h2>
                    <p class="text-xs">Aloon - Aloon Priok Nomor 27, Perak Barat, Krembangan, Kota Surabaya, Jawa Timur 60177</p>
                </div>
                <div class="border-t-2 border-[#1e3a8b] my-4"></div>

                <div class="text-xl font-bold text-center mb-8">INVOICE</div>

                {{-- Meta Info --}}
                <div class="grid grid-cols-2 gap-8 mb-8 text-sm">
                    <div>
                        <div class="font-bold">Andalan Maritim Sejahtera, PT</div>
                        <div>Aloon-Aloon Priok No. 27</div>
                        <div>Perak Barat, Krembangan</div>
                        <div>Kota Surabaya, Provinsi Jawa Timur 60177</div>

                        <div class="mt-6 mb-1">Bill To:</div>
                        <div class="font-bold">PT. MERATUS LINE</div>
                        <div class="text-xs">JL ALOON-ALOON PRIOK NO.27 RT 006 RW 008, PERAK<br>BARAT, KREMBANGAN , KOTA SURABAYA, JAWA TIMUR</div>
                    </div>
                    <div>
                        <table class="w-full text-sm">
                            <tr>
                                <td class="text-right py-1 pr-4">Invoice Number :</td>
                                {{-- Background yellow dihapus disini --}}
                                <td class="font-bold px-2" id="preview-inv-number">INV-AMS-{{ str_pad($upload->id, 5, '0', STR_PAD_LEFT) }}</td>
                            </tr>
                            <tr>
                                <td class="text-right py-1 pr-4">Invoice Date :</td>
                                <td id="preview-inv-date">{{ now()->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-right py-1 pr-4">Tanggal Pemesanan :</td>
                                <td>{{ $upload->tanggal_pemesanan ?? now()->subDays(4)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-right py-1 pr-4">Tanggal Pengiriman :</td>
                                <td>{{ $upload->tanggal_pengiriman ?? now()->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-right py-1 pr-4">No. Surat Jalan/DO :</td>
                                <td>{{ $upload->no_do ?? '009/DO-AMS-LBJ/III/2026' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Main Table --}}
                <table class="w-full border-collapse border border-black text-sm mb-0">
                    <thead>
                        <tr class="border border-black bg-white">
                            <th class="border border-black py-2 px-3 w-12">No</th>
                            <th class="border border-black py-2 px-3 text-center">DESCRIPTION</th>
                            <th class="border border-black py-2 px-3 w-48">AMOUNT (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border-l border-r border-black py-2 px-3 text-center">1</td>
                            <td class="border-l border-r border-black py-2 px-3">Pembelian Ransum Vessel &nbsp;{{ strtoupper($upload->vessel_name) }}</td>
                            <td class="border-l border-r border-black py-2 px-3">
                                <div class="flex justify-between">
                                    <span>Rp</span>
                                    <span>{{ number_format($upload->total_belanja_ransum, 0, '.', ',') }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="border-l border-r border-black py-2 px-3 text-center">2</td>
                            <td class="border-l border-r border-black py-2 px-3">No. PO * : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $upload->po_number ?? '000' }}</td>
                            <td class="border-l border-r border-black py-2 px-3"></td>
                        </tr>
                        <tr>
                            <td class="border-l border-r border-black py-2 px-3 text-center">3</td>
                            <td class="border-l border-r border-black py-2 px-3">Voy * : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $upload->voyage ?? '-' }}</td>
                            <td class="border-l border-r border-black py-2 px-3"></td>
                        </tr>
                        <tr>
                            <td class="border-l border-r border-black py-2 px-3 text-center pb-24">4</td>
                            <td class="border-l border-r border-black py-2 px-3 pb-24">Port * : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $upload->port_tujuan ?? '-' }}</td>
                            <td class="border-l border-r border-black py-2 px-3 pb-24"></td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="border border-black py-2 px-3 text-right font-bold">TOTAL :</td>
                            <td class="border border-black py-2 px-3 font-bold">
                                <div class="flex justify-between">
                                    <span>Rp</span>
                                    <span>{{ number_format($upload->total_belanja_ransum, 0, '.', ',') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="font-normal px-3 py-1.5 text-sm mb-12 border-l border-r border-b border-black">
                    Says &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <span class="italic">{{ $teksTerbilang ?? '#ERROR!' }}</span>
                </div>

                {{-- Payment & TTD --}}
                <div class="flex justify-between text-sm">
                    <div>
                        <div class="mb-1">Paid to * :</div>
                        <div>ANDALAN MARITIM SEJAHTERA, PT</div>
                        <div>Bank Mandiri</div>
                        <div>KCP Surabaya Tanjung Perak</div>
                        <div>A/C No * : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;140-05-8808889-9 &nbsp;IDR</div>
                    </div>
                    
                    {{-- pt-40 (padding top) dirubah dari 24 ke 40 agar namanya turun jauh ke bawah --}}
                    <div class="text-center pt-40 pr-20 w-64">
                        <div class="border-b border-black inline-block px-4 pb-1">Irwinsyah</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function updateInvoiceDate(val) {
            if (!val) return;
            const d = new Date(val + 'T00:00:00');
            const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            document.getElementById('preview-inv-date').textContent =
                (d.getDate() < 10 ? '0'+d.getDate() : d.getDate()) + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }
    </script>
</x-app-layout>