<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Purchase Order – {{ $upload->vessel_name ?? $upload->original_filename }}
            </h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">← Kembali</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Barang telah dikategorikan per vendor. Anda bisa langsung mengedit kolom seperti format Excel di bawah ini.
            </div>

            @if(empty($grouped))
                <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                    <p class="text-gray-400">{{ __('Tidak ada item pada data ini.') }}</p>
                </div>
            @else
                {{-- Vendor Tabs --}}
                @php $vendors = array_keys($grouped); @endphp
                <div class="mb-4 flex flex-wrap gap-2" id="vendor-tabs">
                    @foreach($vendors as $vendor)
                        <button type="button" onclick="showVendorTab('{{ Illuminate\Support\Str::slug($vendor) }}')" id="tab-{{ Illuminate\Support\Str::slug($vendor) }}"
                            class="tab-btn px-4 py-2 text-sm font-medium rounded-lg border transition {{ $loop->first ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                            {{ $vendor }} <span class="ml-1 text-xs opacity-75">({{ count($grouped[$vendor]) }})</span>
                        </button>
                    @endforeach
                </div>

                @foreach($grouped as $vendor => $items)
                @php
                    $vendorSlug = Illuminate\Support\Str::slug($vendor);
                    $vendorDetails = $vendorDetailsBySlug[$vendorSlug] ?? null;
                    $grandTotal = 0;
                    foreach ($items as $idx => $item) {
                        $itemPoPrice = $poPricesByVendor[$vendorSlug][$idx] ?? 0;
                        $grandTotal += ($itemPoPrice * ($item->qty ?? 0));
                    }
                @endphp
                
                <div id="panel-{{ $vendorSlug }}" class="vendor-panel {{ !$loop->first ? 'hidden' : '' }}">
                    <form method="POST" action="{{ route('admin.ransum.po.download', [$upload->id, $vendorSlug]) }}" class="po-form" target="_blank">
                        @csrf
                        <div class="bg-slate-50 shadow-inner p-8 overflow-x-auto mb-4 border border-gray-300 rounded-xl">
                            
                            {{-- EXCEL STYLE WRAPPER (PAPER PREVIEW) --}}
                            <div style="min-width: 900px; font-family: Arial, sans-serif; font-size: 12px; color: #000; padding: 40px 45px; background: #ffffff; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); border: 1px solid #e5e7eb; border-radius: 4px;">
                                
                                {{-- Header Titles --}}
                                <div class="flex justify-between font-bold text-lg mb-2 px-1">
                                    <div>PURCHASE ORDER</div>
                                    <div>PT ANDALAN MARITIM SEJAHTERA</div>
                                </div>

                                {{-- Info Box --}}
                                <table class="excel-table w-full mb-2">
                                    <tr>
                                        <td class="w-1/2 p-0 align-top border-r-0">
                                            <table class="w-full h-full border-none">
                                                <tr><td class="w-32 border-none border-b border-r border-black p-1">PO No.</td><td class="w-4 border-none border-b border-black text-center p-1">:</td><td class="border-none border-b border-black p-0">
                                                    
                                                        @php
                                                            $romans = ['01'=>'I','02'=>'II','03'=>'III','04'=>'IV','05'=>'V','06'=>'VI','07'=>'VII','08'=>'VIII','09'=>'IX','10'=>'X','11'=>'XI','12'=>'XII'];
                                                            $romanMonth = $romans[now()->format('m')];
                                                            $year = now()->format('Y');
                                                            
                                                            // Ambil semua vendor untuk menentukan urutan
                                                            $allVendors = array_keys($grouped);
                                                            $currentIndex = array_search($vendor, $allVendors); // Dimulai dari 0, 1, 2...
                                                            
                                                            // Angka progresif: ID dasar ditambah urutan vendor, lalu jadikan 3 digit
                                                            $progressiveNumber = $upload->id + $currentIndex;
                                                            $paddedNumber = str_pad($progressiveNumber, 3, '0', STR_PAD_LEFT);

                                                            // Format Akhir: 041/AMS-PO-LBJ/III/2026
                                                            $defaultPoNumber = "{$paddedNumber}/AMS-PO-LBJ/{$romanMonth}/{$year}";

                                                            // Cek apakah admin sudah pernah menyimpannya di database
                                                            $existingPoData = is_string($upload->po_number) && str_starts_with(trim($upload->po_number), '{') 
                                                                            ? json_decode($upload->po_number, true) 
                                                                            : [];
                                                            $savedPoNumber = $existingPoData[$vendorSlug] ?? $defaultPoNumber;
                                                        @endphp

                                                        <input type="text" name="po_number" value="{{ $savedPoNumber }}" class="excel-input font-bold">
                                            </td></tr>
                                                <tr><td class="border-none border-b border-r border-black p-1">Request Date</td><td class="border-none border-b border-black text-center p-1">:</td><td class="border-none border-b border-black p-0"><input type="date" name="po_date" value="{{ now()->format('Y-m-d') }}" class="excel-input"></td></tr>
                                                <tr><td class="border-none border-r border-black p-1">Delivery Date</td><td class="border-none text-center p-1">:</td><td class="border-none p-0"><input type="date" name="delivery_date" value="{{ $upload->delivery_date ? \Carbon\Carbon::parse($upload->delivery_date)->format('Y-m-d') : now()->addDays(3)->format('Y-m-d') }}" class="excel-input"></td></tr>
                                            </table>
                                        </td>
                                        <td class="w-1/2 p-0 align-top border-l-0">
                                            <table class="w-full h-full border-none">
                                                <tr><td class="w-24 border-none border-b border-r border-black p-1">Ves</td><td class="w-4 border-none border-b border-black text-center p-1">:</td><td class="border-none border-b border-black p-0"><input type="text" name="vessel_name" value="{{ $upload->vessel_name }}" class="excel-input"></td></tr>
                                                <tr><td class="border-none border-b border-r border-black p-1">ETB</td><td class="border-none border-b border-black text-center p-1">:</td><td class="border-none border-b border-black p-0"><input type="text" name="etb" value="" placeholder="dd/mm/yyyy hh:mm" class="excel-input"></td></tr>
                                                <tr><td colspan="3" class="border-none bg-gray-50 h-6"></td></tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                {{-- Vendor & Ship To --}}
                                <table class="excel-table w-full mb-2">
                                    <tr>
                                        <th class="w-1/2 bg-yellow-300 text-center font-bold p-1">Vendor</th>
                                        <th class="w-1/2 bg-yellow-300 text-center font-bold p-1">Ship To</th>
                                    </tr>
                                    <tr>
                                        <td class="p-0 align-top border-r-0">
                                            <table class="w-full border-none">
                                                <tr><td class="w-24 border-none p-1">Vendor Name</td><td class="w-4 border-none p-1">:</td><td class="border-none p-0"><input type="text" name="vendor_name" value="{{ $vendorDetails['name'] ?? $vendor }}" class="excel-input font-bold"></td></tr>
                                                <tr><td class="border-none p-1">PIC</td><td class="border-none p-1">:</td><td class="border-none p-0"><input type="text" name="vendor_contact_name" value="{{ $vendorDetails['contact_name'] ?? '' }}" class="excel-input"></td></tr>
                                                <tr><td class="border-none p-1 align-top">Address</td><td class="border-none p-1 align-top">:</td><td class="border-none p-0"><textarea name="vendor_address" rows="2" class="excel-input resize-none">{{ $vendorDetails['address'] ?? '' }}</textarea></td></tr>
                                                <tr><td class="border-none p-1">Phone</td><td class="border-none p-1">:</td><td class="border-none p-0"><input type="text" name="vendor_phone" value="{{ $vendorDetails['phone'] ?? '' }}" class="excel-input"></td></tr>
                                                <tr><td class="border-none p-1">Email</td><td class="border-none p-1">:</td><td class="border-none p-0"><input type="text" name="vendor_email" value="{{ $vendorDetails['email'] ?? '' }}" class="excel-input text-blue-600"></td></tr>
                                            </table>
                                        </td>
                                        <td class="p-0 align-top border-l-0 border-l border-black">
                                            <table class="w-full border-none">
                                                <tr><td colspan="3" class="border-none p-0"><input type="text" name="deliver_to" value="PT Andalan Maritim Sejahtera (WH JKT)" class="excel-input font-bold w-full bg-gray-50"></td></tr>
                                                <tr><td class="w-24 border-none p-1">PIC</td><td class="w-4 border-none p-1">:</td><td class="border-none p-0"><input type="text" name="ship_to_pic" value="IRWINSYAH" class="excel-input"></td></tr>
                                                <tr><td class="border-none p-1 align-top">Address</td><td class="border-none p-1 align-top">:</td><td class="border-none p-0"><textarea name="ship_to_address" rows="2" class="excel-input resize-none">Pergudangan INKOPAU, Jl. RE Martadinata No. 100 Blok. B03</textarea></td></tr>
                                                <tr><td class="border-none p-1">Phone</td><td class="border-none p-1">:</td><td class="border-none p-0"><input type="text" name="ship_to_phone" value="+62 857-8211-6756" class="excel-input"></td></tr>
                                                <tr><td class="border-none p-1">Email</td><td class="border-none p-1">:</td><td class="border-none p-0"><input type="text" name="ship_to_email" value="irwinsyah.razi16@gmail.com" class="excel-input text-blue-600 underline"></td></tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                {{-- Items --}}
                                <table class="excel-table w-full mb-2">
                                    <thead>
                                        <tr class="bg-yellow-300">
                                            <th class="w-8 p-1 text-center font-bold">N</th>
                                            <th class="p-1 text-center font-bold">Item</th>
                                            <th class="p-1 text-center font-bold">Description</th>
                                            <th class="w-16 p-1 text-center font-bold">Qt</th>
                                            <th class="w-20 p-1 text-center font-bold">UOM</th>
                                            <th class="w-24 p-1 text-center font-bold">Unit Price</th>
                                            <th class="w-28 p-1 text-center font-bold">Total Price</th>
                                            <th class="w-40 p-1 text-center font-bold">Supplier</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $idx => $item)
                                        @php
                                            $itemPoPrice = $poPricesByVendor[$vendorSlug][$idx] ?? 0;
                                            $sub = ($itemPoPrice * ($item->qty ?? 0));
                                        @endphp
                                        <tr>
                                            <td class="p-1 text-center">{{ $idx + 1 }}</td>
                                            <td class="p-0"><input type="text" name="items[{{ $idx }}][nama_ransum]" value="{{ $item->nama_ransum }}" class="excel-input"></td>
                                            <td class="p-0"><input type="text" name="items[{{ $idx }}][keterangan]" value="{{ $item->ket_remarks }}" class="excel-input"></td>
                                            <td class="p-0"><input type="number" name="items[{{ $idx }}][qty]" value="{{ $item->qty }}" step="any" class="excel-input text-center item-qty-{{ $vendorSlug }}" data-idx="{{ $idx }}"></td>
                                            <td class="p-0"><input type="text" name="items[{{ $idx }}][satuan]" value="{{ $item->satuan }}" class="excel-input text-center"></td>
                                            <td class="p-0"><input type="number" name="items[{{ $idx }}][harga]" value="{{ $itemPoPrice }}" class="excel-input text-right item-price-{{ $vendorSlug }}" data-idx="{{ $idx }}"></td>
                                            <td class="p-0"><input type="number" name="items[{{ $idx }}][subtotal]" value="{{ $sub }}" class="excel-input text-right item-subtotal-{{ $vendorSlug }} bg-gray-100" readonly></td>
                                            <td class="p-1 text-center text-sm">{{ $vendor }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{-- Notes & Summary --}}
                                <table class="excel-table w-full mb-6">
                                    <tr>
                                        <td class="w-1/2 p-0 align-top border-r-0">
                                            <table class="w-full border-none">
                                                <tr><td class="bg-yellow-300 font-bold text-center p-1 border-none border-b border-r border-black">Note's and Instructions</td></tr>
                                                <tr><td class="p-0 border-none border-r border-black"><textarea name="notes" rows="4" class="excel-input w-full resize-none bg-yellow-50"></textarea></td></tr>
                                            </table>
                                        </td>
                                        <td class="w-1/2 p-0 align-top border-l-0">
                                            <table class="w-full border-none">
                                                <tr><td class="w-1/2 p-1 border-none border-b border-r border-black pl-2">Sub Total</td><td class="w-4 border-none border-b border-black text-center p-1">:</td><td class="border-none border-b border-black p-0 text-right"><span class="block p-1 font-bold" id="sub-total-{{ $vendorSlug }}">{{ number_format($grandTotal, 0, '', '') }}</span></td></tr>
                                                <tr><td class="p-1 border-none border-b border-r border-black pl-2">Discount %</td><td class="border-none border-b border-black text-center p-1">:</td><td class="border-none border-b border-black p-0"><input type="text" name="discount" class="excel-input text-right"></td></tr>
                                                <tr><td class="p-1 border-none border-b border-r border-black pl-2">VAT (11%)</td><td class="border-none border-b border-black text-center p-1">:</td><td class="border-none border-b border-black p-0"><input type="text" name="vat" class="excel-input text-right"></td></tr>
                                                <tr><td class="p-1 border-none border-b border-r border-black pl-2">Shipping</td><td class="border-none border-b border-black text-center p-1">:</td><td class="border-none border-b border-black p-0"><input type="text" name="shipping" class="excel-input text-right"></td></tr>
                                                <tr><td class="p-1 border-none border-r border-black font-bold pl-2">TOTAL</td><td class="border-none border-black font-bold text-center p-1">:</td><td class="border-none border-black p-0 text-right"><span class="block p-1 font-bold text-lg" id="grand-total-{{ $vendorSlug }}">{{ number_format($grandTotal, 0, '', '') }}</span></td></tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                {{-- Signatures --}}
                                <div class="flex justify-between mt-8 text-center px-16">
                                    <div class="w-48">
                                        <div class="font-bold mb-16">Supplier</div>
                                        <div class="border-b border-black inline-block min-w-full pb-1"><input type="text" class="text-center outline-none bg-transparent w-full" placeholder="Nama Supplier"></div>
                                    </div>
                                    <div class="w-48">
                                        <div class="font-bold mb-16">Procurement</div>
                                        <div class="border-b border-black inline-block min-w-full pb-1"><input type="text" name="prepared_by" value="Irwinsyah" class="text-center outline-none bg-transparent w-full"></div>
                                    </div>
                                </div>

                            </div>
                            {{-- END EXCEL WRAPPER --}}

                        </div>

                        <div class="flex justify-end mb-8">
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition shadow">
                                Download PDF – {{ $vendor }}
                            </button>
                        </div>
                    </form>
                </div>
                @endforeach
            @endif

        </div>
    </div>

    <style>
        .excel-table { border-collapse: collapse; }
        .excel-table th, .excel-table td { border: 1px solid #000; }
        .excel-input { 
            width: 100%; border: none; outline: none; background: transparent; 
            padding: 4px; font-family: inherit; font-size: inherit; color: inherit;
        }
        .excel-input:focus { background-color: #e0f2fe; }
    </style>

    <script>
        function showVendorTab(slug) {
            document.querySelectorAll('.vendor-panel').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
                btn.classList.add('bg-white', 'text-gray-600', 'border-gray-300');
            });
            const panel = document.getElementById('panel-' + slug);
            if (panel) panel.classList.remove('hidden');
            const tab = document.getElementById('tab-' + slug);
            if (tab) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('bg-white', 'text-gray-600', 'border-gray-300');
            }
        }

        @foreach($grouped as $vendor => $items)
        @php $vendorSlug = Illuminate\Support\Str::slug($vendor); @endphp
        (function(slug) {
            function updateTotal() {
                let total = 0;
                document.querySelectorAll('.item-subtotal-' + slug).forEach(el => {
                    total += parseFloat(el.value) || 0;
                });
                
                // Format ribuan
                let formatted = new Intl.NumberFormat('id-ID').format(total);
                
                const subDisplay = document.getElementById('sub-total-' + slug);
                if (subDisplay) subDisplay.textContent = formatted;
                
                const grandDisplay = document.getElementById('grand-total-' + slug);
                if (grandDisplay) grandDisplay.textContent = formatted;
            }
            
            document.querySelectorAll('.item-qty-' + slug + ', .item-price-' + slug).forEach(input => {
                input.addEventListener('input', function() {
                    const idx = this.dataset.idx;
                    const qty = parseFloat(document.querySelector('.item-qty-' + slug + '[data-idx="' + idx + '"]').value) || 0;
                    const price = parseFloat(document.querySelector('.item-price-' + slug + '[data-idx="' + idx + '"]').value) || 0;
                    const subEl = document.querySelector('.item-subtotal-' + slug + '[data-idx="' + idx + '"]');
                    if (subEl) subEl.value = qty * price;
                    updateTotal();
                });
            });
        })('{{ $vendorSlug }}');
        @endforeach
    </script>
</x-app-layout>