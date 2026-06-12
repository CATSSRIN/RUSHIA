<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Import BPB Ransum') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Upload Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ __('Upload File BPB Ransum') }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ __('Upload file Excel dengan format template BPB Ransum Meratus (xlsx/xls, maks. 10 MB).') }}</p>
                <form method="POST" action="{{ route('admin.ransum.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
                        <div class="flex-1">
                            <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Pilih File') }} <span class="text-gray-400 font-normal">(xlsx, xls — maks 10 MB)</span>
                            </label>
                            <input
                                type="file"
                                id="excel_file"
                                name="excel_file"
                                accept=".xlsx,.xls"
                                class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent p-2"
                                required
                            >
                        </div>
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition whitespace-nowrap">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            {{ __('Upload & Preview') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Uploads List Header -->
            <div class="flex items-center justify-between mb-4 mt-8">
                <h3 class="text-lg font-bold text-gray-800">{{ __('Riwayat Upload') }}</h3>
                <span class="text-xs font-semibold bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-xl border border-indigo-200 shadow-sm">
                    Total: {{ $uploads->count() }} upload
                </span>
            </div>

            @if($uploads->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
                    <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-400 font-medium">{{ __('Belum ada file yang diupload.') }}</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($uploads as $upload)
                        @php
                            $hasGroupedItems = !empty($upload->grouped_items_by_vendor);
                            $vendorCount = $hasGroupedItems ? count($upload->grouped_items_by_vendor) : 0;
                            $poCount = $upload->pos->count();
                        @endphp
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition duration-200">
                            <!-- Card Header -->
                            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-slate-50 to-white">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <!-- Status Badge -->
                                        @if($upload->status === 'imported')
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                Imported
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                                Pending
                                            </span>
                                        @endif
                                        
                                        <!-- Excel File Icon and Name -->
                                        <div class="flex items-center gap-1.5 text-gray-600 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-xl">
                                            <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700 truncate max-w-xs sm:max-w-md" title="{{ $upload->original_filename }}">{{ $upload->original_filename }}</span>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex flex-wrap gap-2 items-center">
                                        @if($upload->status === 'imported')
                                            <a href="{{ route('admin.ransum.preview', $upload->id) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-100 bg-slate-50 px-3.5 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-100 transition shadow-sm gap-1">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                {{ __('Preview') }}
                                            </a>
                                            <a href="{{ route('admin.ransum.do.preview', $upload->id) }}" class="inline-flex items-center justify-center rounded-lg border border-blue-200 bg-blue-50 px-3.5 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-100 transition shadow-sm gap-1">
                                                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                {{ __('Buat DO') }}
                                            </a>
                                            <a href="{{ route('admin.ransum.invoice', $upload->id) }}" class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-1.5 text-sm font-medium text-emerald-700 hover:bg-emerald-100 transition shadow-sm gap-1">
                                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                {{ __('Buat Invoice') }}
                                            </a>
                                        @else
                                            <a href="{{ route('admin.ransum.preview', $upload->id) }}" class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-3.5 py-1.5 text-sm font-medium text-indigo-700 hover:bg-indigo-100 transition shadow-sm gap-1">
                                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                {{ __('Preview & Import') }}
                                            </a>
                                        @endif

                                        <form method="POST" action="{{ route('admin.ransum.destroy', $upload->id) }}" onsubmit="return confirm('{{ __('Hapus upload ini?') }}')" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 px-3.5 py-1.5 text-sm font-medium text-red-600 hover:bg-red-100 transition shadow-sm gap-1">
                                                <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body (Grid layout) -->
                            <div class="p-6 grid gap-6 xl:grid-cols-[1.1fr_1fr_1.1fr]">
                                <!-- Col 1: Detail Kapal -->
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="text-xl font-bold text-gray-900 leading-tight">
                                            {{ $upload->vessel_name ?? __('Kapal Belum Diidentifikasi') }}
                                        </h4>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Voyage: <span class="font-semibold text-gray-700">{{ $upload->voyage ?? '-' }}</span> &bull; 
                                            <span class="font-semibold text-gray-700">{{ $upload->items->count() }}</span> item
                                        </p>
                                    </div>
                                    <div class="grid gap-3 sm:grid-cols-2 mt-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">{{ __('Diupload Oleh') }}</p>
                                            <p class="text-xs font-semibold text-slate-700 mt-0.5">{{ $upload->uploader->name ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">{{ __('Tanggal Upload') }}</p>
                                            <p class="text-xs font-semibold text-slate-700 mt-0.5">{{ $upload->created_at->format('d M Y · H:i') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Col 2: Ringkasan Item -->
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-bold text-gray-800">{{ __('Ringkasan Item') }}</h4>
                                        @if($vendorCount > 0)
                                            <span class="text-xs text-slate-400 font-semibold">{{ $vendorCount }} vendor</span>
                                        @endif
                                    </div>
                                    @if($hasGroupedItems && $vendorCount > 0)
                                        <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                            @php $shownVendors = 0; @endphp
                                            @foreach($upload->grouped_items_by_vendor as $vendorName => $items)
                                                @php $shownVendors++; @endphp
                                                @if($shownVendors <= 3)
                                                    <div class="flex items-center justify-between bg-emerald-50/50 border border-emerald-100 rounded-xl px-3.5 py-2 text-xs">
                                                        <span class="font-semibold text-emerald-950 truncate" title="{{ $vendorName }}">{{ $vendorName }}</span>
                                                        <span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-800 px-2 py-0.5 font-bold shrink-0">
                                                            {{ count($items) }} item
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                            @if($vendorCount > 3)
                                                <div class="text-center text-[11px] text-gray-400 font-medium pt-1">
                                                    + {{ $vendorCount - 3 }} vendor lainnya
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="rounded-xl border border-dashed border-gray-200 p-6 text-center text-xs text-gray-400 flex flex-col items-center justify-center h-28">
                                            <svg class="w-6 h-6 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <span>Belum diimport / data item kosong.</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Col 3: Surat PO Vendor -->
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-bold text-gray-800">{{ __('Surat PO Vendor') }}</h4>
                                        @if($poCount > 0)
                                            <span class="text-xs text-slate-400 font-semibold">{{ $poCount }} PO</span>
                                        @endif
                                    </div>
                                    @if($poCount > 0)
                                        <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                            @php $shownPos = 0; @endphp
                                            @foreach($upload->pos as $po)
                                                @php $shownPos++; @endphp
                                                @if($shownPos <= 3)
                                                    @php
                                                        $poStatusClass = [
                                                            'menunggu' => 'bg-amber-50 text-amber-800 border-amber-200',
                                                            'diproses' => 'bg-blue-50 text-blue-800 border-blue-200',
                                                            'selesai' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                                        ][$po->status] ?? 'bg-gray-50 text-gray-800 border-gray-200';
                                                    @endphp
                                                    <div class="bg-slate-50/50 border border-slate-200/60 rounded-xl p-2.5 text-xs space-y-1">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <span class="font-bold text-slate-700 truncate" title="{{ $po->vendor_name }}">{{ $po->vendor_name }}</span>
                                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold border shrink-0 {{ $poStatusClass }}">
                                                                {{ ucfirst($po->status) }}
                                                            </span>
                                                        </div>
                                                        <div class="text-[10px] text-gray-400 font-semibold truncate">{{ $po->po_number }}</div>
                                                    </div>
                                                @endif
                                            @endforeach
                                            @if($poCount > 3)
                                                <div class="text-center text-[11px] text-gray-400 font-medium pt-1">
                                                    + {{ $poCount - 3 }} PO lainnya
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="rounded-xl border border-dashed border-gray-200 p-6 text-center text-xs text-gray-400 flex flex-col items-center justify-center h-28">
                                            <svg class="w-6 h-6 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span>Belum ada PO yang dibuat.</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
