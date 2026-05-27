<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Preview List Ransum AMS') }}: {{ $upload->vessel_name ?? $upload->original_filename }}
            </h2>
            <a href="{{ route('admin.orders.index') }}"
               class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Kembali ke Orders') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 overflow-x-auto">
            
            <div class="bg-white p-6 shadow-sm border border-gray-200" style="min-width: 1200px;">
                
                {{-- Printable Area --}}
                <div id="printable-area" contenteditable="true" class="outline-none">
                {{-- Top Header AMS --}}
                <div style="background-color: #d1b3ff; text-align: center; font-weight: bold; font-size: 16px; padding: 4px; border: 1px solid black;">
                    AMS
                </div>
                
                {{-- Info Section --}}
                <div style="font-weight: bold; font-size: 14px; margin-top: 8px; margin-bottom: 8px;">
                    <div id="vessel-title" style="text-transform: uppercase;">{{ $upload->vessel_name ?? 'UNKNOWN' }} ({{ $upload->jumlah_hari_pensupplaian ?? '-' }} Hari)</div>
                    <div>ETB : {{ $upload->eta ?? '-' }}</div>
                    <div>ETD : {{ $upload->date_end ?? '-' }}</div>
                </div>

                {{-- Table --}}
                <table style="width: 100%; border-collapse: collapse; font-size: 11px; font-family: Arial, sans-serif;">
                    <thead>
                        <tr style="background-color: #ffe699;">
                            <th rowspan="2" style="border: 1px solid black; padding: 4px; width: 3%;">No.</th>
                            <th rowspan="2" style="border: 1px solid black; padding: 4px; width: 5%;">Kode</th>
                            <th rowspan="2" style="border: 1px solid black; padding: 4px; width: 20%;">NAMA RANSUM</th>
                            <th rowspan="2" style="border: 1px solid black; padding: 4px; width: 15%;">ITEMS</th>
                            <th rowspan="2" style="border: 1px solid black; padding: 4px; width: 10%;">MERK</th>
                            <th colspan="3" style="border: 1px solid black; padding: 4px; text-align: center;">AMS</th>
                            <th colspan="1" style="border: 1px solid black; padding: 4px; text-align: center;">PEMESANAN</th>
                            <th rowspan="2" style="border: 1px solid black; padding: 4px; width: 12%;">REMARKS</th>
                        </tr>
                        <tr style="background-color: #ffe699; text-align: center;">
                            <th style="border: 1px solid black; padding: 4px;">HARGA<br>SATUAN<br>(Rp)</th>
                            <th style="border: 1px solid black; padding: 4px;">SATUAN<br>PCS</th>
                            <th style="border: 1px solid black; padding: 4px;">JML<br>QTY</th>
                            <th style="border: 1px solid black; padding: 4px;">TOTAL HARGA<br>(Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sectionLetters = range('A', 'Z');
                            $secIdx = 0;
                        @endphp
                        
                        @foreach($grouped as $sectionName => $items)
                            {{-- Section Header --}}
                            <tr>
                                <td style="border: 1px solid black; padding: 4px; text-align: center; font-weight: bold;">{{ $sectionLetters[$secIdx] ?? '' }}</td>
                                <td colspan="9" style="border: 1px solid black; padding: 4px; font-weight: bold;">{{ $sectionName }}</td>
                            </tr>
                            
                            {{-- Items --}}
                            @foreach($items as $idx => $item)
                                <tr>
                                    <td style="border: 1px solid black; padding: 4px; text-align: center;">{{ $idx + 1 }}</td>
                                    <td style="border: 1px solid black; padding: 4px; text-align: center;">{{ $item['kode'] }}</td>
                                    <td style="border: 1px solid black; padding: 4px;">{{ $item['nama_ransum'] }}</td>
                                    <td style="border: 1px solid black; padding: 4px;">{{ $item['items'] }}</td>
                                    <td style="border: 1px solid black; padding: 4px;">{{ $item['merk_spec'] }}</td>
                                    <td style="border: 1px solid black; padding: 4px; text-align: right;">{{ number_format($item['ams_harga_jual'], 0, ',', '.') }}</td>
                                    <td style="border: 1px solid black; padding: 4px;">{{ $item['ams_satuan'] }}</td>
                                    <td style="border: 1px solid black; padding: 4px; text-align: right;">{{ number_format($item['qty'], 2, '.', ',') }}</td>
                                    <td style="border: 1px solid black; padding: 4px; text-align: right;">{{ number_format($item['pemesanan_harga_beli'], 0, ',', '.') }}</td>
                                    <td style="border: 1px solid black; padding: 4px;">{{ $item['remarks'] }}</td>
                                </tr>
                            @endforeach
                            
                            {{-- Empty row after section --}}
                            <tr>
                                <td colspan="10" style="border: 1px solid black; padding: 4px;">&nbsp;</td>
                            </tr>
                            @php $secIdx++; @endphp
                        @endforeach
                        
                    </tbody>
                </table>
                </div>
                
                {{-- Print Button --}}
                <form method="POST" action="{{ route('admin.ransum.list.download', $upload->id) }}" id="pdfForm">
                    @csrf
                    <input type="hidden" name="html_content" id="html_content">
                    <div class="mt-6 flex justify-end gap-3 print:hidden" contenteditable="false" id="action-buttons">
                        <button type="button" onclick="printDocument()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold text-sm inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print Browser
                        </button>
                        <button type="button" onclick="downloadBackendPdf()" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold text-sm inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download PDF
                        </button>
                    </div>
                </form>
                
                <style>
                    @media print {
                        body { background: white; }
                        .max-w-full { max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
                        .bg-white { box-shadow: none !important; border: none !important; padding: 0 !important; min-width: 100% !important; }
                        table { width: 100% !important; }
                    }
                </style>
                
                <script>
                    function printDocument() {
                        const originalTitle = document.title;
                        const vesselTitle = document.getElementById('vessel-title').innerText.trim().replace(/[^a-zA-Z0-9 ()-]/g, '');
                        document.title = "List Ransum " + vesselTitle;
                        window.print();
                        document.title = originalTitle;
                    }
                    
                    function downloadBackendPdf() {
                        let container = document.getElementById('printable-area');
                        document.getElementById('html_content').value = container.innerHTML;
                        document.getElementById('pdfForm').submit();
                    }
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
