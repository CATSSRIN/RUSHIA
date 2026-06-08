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

            {{-- Warning: DO belum dibuat --}}
            @if(session('warning'))
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-400 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm text-yellow-800 font-medium">{{ session('warning') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.ransum.invoice.download', $upload->id) }}" id="invoice-form">
                @csrf

                {{-- Action Bar --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 flex justify-between items-center">
                    <div class="text-sm text-gray-500 font-medium">
                        {{ __('Tip: Anda dapat mengedit semua parameter langsung pada dokumen invoice di bawah.') }}
                    </div>
                    <div>
                        {{-- Tombol download: conditional berdasarkan status DO --}}
                        @if(!empty($upload->no_do))
                            {{-- DO sudah ada: tombol aktif --}}
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ __('Download PDF') }}
                            </button>
                        @else
                            {{-- DO belum ada: tombol disabled + link ke DO --}}
                            <div class="flex gap-2">
                                <a href="{{ route('admin.ransum.do.preview', $upload->id) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-yellow-500 text-white text-sm font-semibold rounded-lg hover:bg-yellow-600 transition">
                                    {{ __('Buat DO Terlebih Dahulu') }}
                                </a>
                                <button type="button" disabled class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-400 text-white text-sm font-semibold rounded-lg cursor-not-allowed opacity-60" title="{{ __('Anda harus membuat DO terlebih dahulu sebelum mendownload invoice') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ __('Download PDF') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Invoice Document Area --}}
                <div class="bg-white shadow-sm border border-gray-200 p-10 print:shadow-none print:border-none" style="font-family: Arial, sans-serif; color: #000;">
                    
                    {{-- Header --}}
                    <div class="text-center">
                        <input type="text" name="header_title" value="PT Andalan Maritim Sejahtera" class="editable-input text-center text-2xl font-bold text-[#1e3a8b] mb-1 tracking-wide focus:ring-1 focus:ring-indigo-500 rounded">
                        <input type="text" name="header_address" value="Aloon - Aloon Priok Nomor 27, Perak Barat, Krembangan, Kota Surabaya, Jawa Timur 60177" class="editable-input text-center text-xs focus:ring-1 focus:ring-indigo-500 rounded">
                    </div>
                    <div class="border-t-2 border-[#1e3a8b] my-4"></div>

                    <div class="text-xl font-bold text-center mb-8">INVOICE</div>

                    {{-- Meta Info --}}
                    <div class="grid grid-cols-2 gap-8 mb-8 text-sm">
                        <div class="space-y-1">
                            <input type="text" name="sender_company" value="Andalan Maritim Sejahtera, PT" class="editable-input font-bold text-sm focus:ring-1 focus:ring-indigo-500 rounded">
                            <input type="text" name="sender_address_1" value="Aloon-Aloon Priok No. 27" class="editable-input text-xs focus:ring-1 focus:ring-indigo-500 rounded">
                            <input type="text" name="sender_address_2" value="Perak Barat, Krembangan" class="editable-input text-xs focus:ring-1 focus:ring-indigo-500 rounded">
                            <input type="text" name="sender_address_3" value="Kota Surabaya, Provinsi Jawa Timur 60177" class="editable-input text-xs focus:ring-1 focus:ring-indigo-500 rounded">

                            <div class="mt-6">
                                <input type="text" name="bill_to_title" value="Bill To:" class="editable-input font-medium text-xs focus:ring-1 focus:ring-indigo-500 rounded">
                            </div>
                            <input type="text" name="bill_to_company" value="PT. MERATUS LINE" class="editable-input font-bold text-sm focus:ring-1 focus:ring-indigo-500 rounded">
                            <textarea name="bill_to_address" rows="2" class="editable-input text-xs focus:ring-1 focus:ring-indigo-500 rounded resize-none leading-normal">JL ALOON-ALOON PRIOK NO.27 RT 006 RW 008, PERAK&#10;BARAT, KREMBANGAN , KOTA SURABAYA, JAWA TIMUR</textarea>
                        </div>
                        <div>
                            <table class="w-full text-sm">
                                <tr>
                                    <td class="text-right py-1 pr-4 whitespace-nowrap">Invoice Number :</td>
                                    <td class="font-bold px-2">
                                        <input type="text" name="invoice_number" id="invoice_number" value="INV-AMS-{{ str_pad($upload->id, 5, '0', STR_PAD_LEFT) }}" class="editable-input font-bold focus:ring-1 focus:ring-indigo-500 rounded">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right py-1 pr-4 whitespace-nowrap">Invoice Date :</td>
                                    <td>
                                        <input type="text" name="invoice_date" id="invoice_date" value="{{ now()->format('d F Y') }}" class="editable-input focus:ring-1 focus:ring-indigo-500 rounded">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right py-1 pr-4 whitespace-nowrap">Tanggal Pemesanan :</td>
                                    <td>
                                        <input type="text" name="tanggal_pemesanan" value="{{ $upload->tanggal_pemesanan ?? now()->subDays(4)->format('d F Y') }}" class="editable-input focus:ring-1 focus:ring-indigo-500 rounded">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right py-1 pr-4 whitespace-nowrap">Tanggal Pengiriman :</td>
                                    <td>
                                        <input type="text" name="tanggal_pengiriman" value="{{ $upload->tanggal_pengiriman ?? now()->format('d F Y') }}" class="editable-input focus:ring-1 focus:ring-indigo-500 rounded">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right py-1 pr-4 whitespace-nowrap">No. Surat Jalan/DO :</td>
                                    <td>
                                        <input type="text" name="no_do" value="{{ $upload->no_do ?? '009/DO-AMS-LBJ/III/2026' }}" class="editable-input focus:ring-1 focus:ring-indigo-500 rounded">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Main Table --}}
                    <table class="w-full border-collapse border border-black text-sm mb-0">
                        <thead>
                            <tr class="border border-black bg-white">
                                <th class="border border-black py-2 px-3 w-12 text-center">No</th>
                                <th class="border border-black py-2 px-3 text-center">DESCRIPTION</th>
                                <th class="border border-black py-2 px-3 w-48 text-center">AMOUNT (IDR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border-l border-r border-black py-2 px-3 text-center">1</td>
                                <td class="border-l border-r border-black py-2 px-3">
                                    <input type="text" name="description_1" value="Pembelian Ransum Vessel {{ strtoupper($upload->vessel_name) }}" class="editable-input focus:ring-1 focus:ring-indigo-500 rounded">
                                </td>
                                <td class="border-l border-r border-black py-2 px-3">
                                    <div class="flex items-center justify-between">
                                        <span>Rp</span>
                                        <input type="number" name="amount_1" id="amount_1" value="{{ (int)$upload->total_belanja_ransum }}" class="editable-input text-right font-semibold focus:ring-1 focus:ring-indigo-500 rounded w-36" oninput="calculateTotal()">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="border-l border-r border-black py-2 px-3 text-center">2</td>
                                <td class="border-l border-r border-black py-2 px-3">
                                    <input type="text" name="description_2" value="No. PO * : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $upload->po_number ?? '000' }}" class="editable-input focus:ring-1 focus:ring-indigo-500 rounded">
                                </td>
                                <td class="border-l border-r border-black py-2 px-3"></td>
                            </tr>
                            <tr>
                                <td class="border-l border-r border-black py-2 px-3 text-center">3</td>
                                <td class="border-l border-r border-black py-2 px-3">
                                    <input type="text" name="description_3" value="Voy * : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $upload->voyage ?? '-' }}" class="editable-input focus:ring-1 focus:ring-indigo-500 rounded">
                                </td>
                                <td class="border-l border-r border-black py-2 px-3"></td>
                            </tr>
                            <tr id="row-4-container">
                                <td class="border-l border-r border-black py-2 px-3 text-center pb-24" id="row-4-no">4</td>
                                <td class="border-l border-r border-black py-2 px-3 pb-24" id="row-4-desc">
                                    <input type="text" name="description_4" value="Port * : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $upload->port_tujuan ?? '-' }}" class="editable-input focus:ring-1 focus:ring-indigo-500 rounded">
                                </td>
                                <td class="border-l border-r border-black py-2 px-3 pb-24" id="row-4-amount"></td>
                            </tr>
                            
                            {{-- Biaya Tambahan Row (Opsi di bawah pojok isi table description) --}}
                            <tr class="border-t border-black bg-gray-50/50">
                                <td class="border-l border-r border-black py-2 px-3 text-center">5</td>
                                <td class="border-l border-r border-black py-2 px-3">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" id="enable_biaya_tambahan" name="enable_biaya_tambahan" value="1" class="rounded text-indigo-600 focus:ring-indigo-500" onchange="toggleBiayaTambahan()">
                                        <input type="text" name="biaya_tambahan_desc" id="biaya_tambahan_desc" value="Biaya Tambahan / Kelebihan Biaya" class="editable-input font-medium focus:ring-1 focus:ring-indigo-500 rounded w-full" disabled>
                                    </div>
                                </td>
                                <td class="border-l border-r border-black py-2 px-3">
                                    <div class="flex items-center justify-between text-gray-400" id="biaya_tambahan_amount_wrapper">
                                        <span>Rp</span>
                                        <input type="number" name="biaya_tambahan_amount" id="biaya_tambahan_amount" value="0" class="editable-input text-right font-semibold focus:ring-1 focus:ring-indigo-500 rounded w-36" oninput="calculateTotal()" disabled>
                                    </div>
                                </td>
                            </tr>

                            <tr class="border border-black">
                                <td colspan="2" class="border border-black py-2 px-3 text-right font-bold">TOTAL :</td>
                                <td class="border border-black py-2 px-3 font-bold">
                                    <div class="flex justify-between">
                                        <span>Rp</span>
                                        <span id="preview-total-amount">{{ number_format($upload->total_belanja_ransum, 0, '.', ',') }}</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="font-normal px-3 py-1.5 text-sm mb-12 border-l border-r border-b border-black flex items-center gap-2">
                        <span>Says &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</span>
                        <input type="text" name="says_text" id="says_text" value="{{ $teksTerbilang }}" class="editable-input italic w-full focus:ring-1 focus:ring-indigo-500 rounded">
                    </div>

                    {{-- Payment & TTD --}}
                    <div class="flex justify-between text-sm">
                        <div class="space-y-1">
                            <input type="text" name="payment_paid_to" value="Paid to * :" class="editable-input font-semibold focus:ring-1 focus:ring-indigo-500 rounded">
                            <input type="text" name="payment_company" value="ANDALAN MARITIM SEJAHTERA, PT" class="editable-input focus:ring-1 focus:ring-indigo-500 rounded">
                            <input type="text" name="payment_bank" value="Bank Mandiri" class="editable-input focus:ring-1 focus:ring-indigo-500 rounded">
                            <input type="text" name="payment_branch" value="KCP Surabaya Tanjung Perak" class="editable-input focus:ring-1 focus:ring-indigo-500 rounded">
                            <input type="text" name="payment_account" value="A/C No * : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;140-05-8808889-9 &nbsp;IDR" class="editable-input focus:ring-1 focus:ring-indigo-500 rounded">
                        </div>
                        
                        <div class="text-center pt-40 pr-20 w-64 flex flex-col items-center">
                            <input type="text" name="signature_name" value="Irwinsyah" class="editable-input text-center border-b border-black rounded-none pb-1 font-semibold focus:ring-1 focus:ring-indigo-500 w-48">
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <style>
        .editable-input {
            background: transparent;
            border: none;
            padding: 2px 4px;
            margin: -2px -4px;
            font-family: inherit;
            font-size: inherit;
            font-weight: inherit;
            color: inherit;
            width: 100%;
            outline: none;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .editable-input:hover {
            background-color: #f3f4f6;
        }
        .editable-input:focus {
            background-color: #fff;
            box-shadow: 0 0 0 2px #818cf8;
        }
        /* Hide spinner for number inputs */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

    <script>
        function penyebut(nilai) {
            nilai = Math.abs(nilai);
            const huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
            let temp = "";
            if (nilai < 12) {
                temp = " " + huruf[nilai];
            } else if (nilai < 20) {
                temp = penyebut(nilai - 10) + " Belas";
            } else if (nilai < 100) {
                temp = penyebut(Math.floor(nilai / 10)) + " Puluh" + penyebut(nilai % 10);
            } else if (nilai < 200) {
                temp = " Seratus" + penyebut(nilai - 100);
            } else if (nilai < 1000) {
                temp = penyebut(Math.floor(nilai / 100)) + " Ratus" + penyebut(nilai % 100);
            } else if (nilai < 2000) {
                temp = " Seribu" + penyebut(nilai - 1000);
            } else if (nilai < 1000000) {
                temp = penyebut(Math.floor(nilai / 1000)) + " Ribu" + penyebut(nilai % 1000);
            } else if (nilai < 1000000000) {
                temp = penyebut(Math.floor(nilai / 1000000)) + " Juta" + penyebut(nilai % 1000000);
            } else if (nilai < 1000000000000) {
                temp = penyebut(Math.floor(nilai / 1000000000)) + " Milyar" + penyebut(fmod(nilai, 1000000000));
            }
            return temp;
        }

        function fmod(a, b) {
            return Number((a - (Math.floor(a / b) * b)).toPrecision(8));
        }

        function terbilang(nilai) {
            if (nilai < 0) {
                return "Minus " + penyebut(nilai).trim() + " Rupiah";
            } else if (nilai === 0) {
                return "Nol Rupiah";
            } else {
                return penyebut(nilai).trim() + " Rupiah";
            }
        }

        function calculateTotal() {
            const amount1 = Number(document.getElementById('amount_1').value) || 0;
            const enableBtn = document.getElementById('enable_biaya_tambahan').checked;
            const biayaTambahan = enableBtn ? (Number(document.getElementById('biaya_tambahan_amount').value) || 0) : 0;
            
            const total = amount1 + biayaTambahan;
            
            // Format to thousands format
            document.getElementById('preview-total-amount').textContent = new Intl.NumberFormat('en-US').format(total);
            
            // Update Says
            document.getElementById('says_text').value = terbilang(total);
        }

        function toggleBiayaTambahan() {
            const checked = document.getElementById('enable_biaya_tambahan').checked;
            const descInput = document.getElementById('biaya_tambahan_desc');
            const amountInput = document.getElementById('biaya_tambahan_amount');
            const wrapper = document.getElementById('biaya_tambahan_amount_wrapper');
            const row4No = document.getElementById('row-4-no');
            const row4Desc = document.getElementById('row-4-desc');
            const row4Amount = document.getElementById('row-4-amount');

            if (checked) {
                descInput.disabled = false;
                amountInput.disabled = false;
                wrapper.classList.remove('text-gray-400');
                
                // Reduce padding on row 4 when row 5 is active
                row4No.classList.remove('pb-24');
                row4No.classList.add('pb-12');
                row4Desc.classList.remove('pb-24');
                row4Desc.classList.add('pb-12');
                row4Amount.classList.remove('pb-24');
                row4Amount.classList.add('pb-12');
            } else {
                descInput.disabled = true;
                amountInput.disabled = true;
                amountInput.value = 0;
                wrapper.classList.add('text-gray-400');
                
                // Restore padding on row 4
                row4No.classList.remove('pb-12');
                row4No.classList.add('pb-24');
                row4Desc.classList.remove('pb-12');
                row4Desc.classList.add('pb-24');
                row4Amount.classList.remove('pb-12');
                row4Amount.classList.add('pb-24');
            }
            calculateTotal();
        }
    </script>
</x-app-layout>
