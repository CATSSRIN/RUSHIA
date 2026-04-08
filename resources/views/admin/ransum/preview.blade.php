<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Preview BPB Ransum') }}: {{ $upload->original_filename }}
            </h2>
            <a href="{{ route('admin.ransum.index') }}"
               class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">{{ session('error') }}</div>
            @endif

            <!-- Header Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ __('Informasi Dokumen') }}</h3>
                    @if($upload->status === 'imported')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('Sudah Diimport') }} — {{ $upload->imported_at?->format('d M Y H:i') }}
                        </span>
                    @else
                        <form method="POST" action="{{ route('admin.ransum.import', $upload->id) }}"
                              onsubmit="return confirm('{{ __('Import semua data dari file ini ke database?') }}')">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                {{ __('Import ke Database') }}
                            </button>
                        </form>
                    @endif
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-3 text-sm">
                    @php
                        $fields = [
                            'vessel_code'              => __('Vessel Code'),
                            'vessel_name'              => __('Vessel Name'),
                            'voyage'                   => __('Voyage'),
                            'contact_person'           => __('Contact Person'),
                            'year'                     => __('Year'),
                            'date_start'               => __('Date Start'),
                            'date_end'                 => __('Date End'),
                            'jumlah_hari_pensupplaian' => __('Jumlah Hari Pensupplaian'),
                            'eta'                      => __('ETA'),
                            'vessel_route'             => __('Vessel Route'),
                            'rute_sekarang'            => __('Rute Sekarang'),
                            'port_tujuan'              => __('Port Tujuan'),
                            'currency'                 => __('Currency'),
                            'conversi_rupiah'          => __('Conversi Rupiah'),
                            'jumlah_crew'              => __('Jumlah Crew'),
                            'vendor_name'              => __('Vendor Name'),
                            'budget'                   => __('Budget'),
                            'total_belanja_ransum'     => __('Total Belanja Ransum'),
                            'barang_non_bkp'           => __('Barang Non BKP'),
                            'barang_bkp'               => __('Barang BKP'),
                            'pajak_11'                 => __('Pajak 11%'),
                            'selisih_anggaran'         => __('Selisih Anggaran'),
                        ];
                    @endphp
                    @foreach($fields as $key => $label)
                        @if($upload->$key !== null && $upload->$key !== '')
                            <div>
                                <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide">{{ $label }}</span>
                                <span class="block text-gray-800 font-medium">
                                    @if(in_array($key, ['budget','total_belanja_ransum','barang_non_bkp','barang_bkp','pajak_11','selisih_anggaran','conversi_rupiah']))
                                        {{ number_format((float)$upload->$key, 0, ',', '.') }}
                                    @else
                                        {{ $upload->$key }}
                                    @endif
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Items per Section -->
            @forelse($sections as $section)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-3 bg-indigo-50 border-b border-indigo-100 flex items-center justify-between">
                        <h4 class="text-base font-semibold text-indigo-900">{{ $section['section'] }}</h4>
                        <span class="text-xs text-indigo-500">{{ count($section['items']) }} {{ __('item') }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">#</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Nama Ransum') }}</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Kode Item') }}</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Items') }}</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Merk/Spec') }}</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('PPN') }}</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Harga Supplier') }}</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Satuan') }}</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Qty') }}</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Non BKP') }}</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('BKP') }}</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('PPN 11%') }}</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Ket. Remarks') }}</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Status Received') }}</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase whitespace-nowrap">{{ __('Good Received') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($section['items'] as $i => $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 text-gray-400">{{ $i + 1 }}</td>
                                        <td class="px-3 py-2 text-gray-800 font-medium whitespace-nowrap">{{ $item['nama_ransum'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $item['kode_item'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $item['items'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $item['merk_spec'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-right text-gray-600">{{ $item['ppn'] !== null ? number_format($item['ppn'], 0, ',', '.') : '-' }}</td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">
                                            {{ $item['supplier'] ?? '-' }}
                                        </td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $item['satuan'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-right text-gray-800">{{ $item['qty'] !== null ? number_format($item['qty'], 0, ',', '.') : '-' }}</td>
                                        <td class="px-3 py-2 text-right text-gray-600">{{ $item['harga'] !== null ? number_format($item['harga'], 0, ',', '.') : '-' }}</td>
                                        <td class="px-3 py-2 text-right text-gray-600">{{ $item['bkp'] !== null ? number_format($item['bkp'], 0, ',', '.') : '-' }}</td>
                                        <td class="px-3 py-2 text-right text-gray-600">{{ $item['ppn_11'] !== null ? number_format($item['ppn_11'], 0, ',', '.') : '-' }}</td>
                                        <td class="px-3 py-2 text-gray-500 max-w-xs truncate">{{ $item['ket_remarks'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $item['status_received'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $item['good_received'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-400">{{ __('Tidak ada data item yang dapat diparse dari file ini.') }}</p>
                </div>
            @endforelse

            <!-- Signature Section – Pemohon & Menyetujui (bottom right) -->
            <div class="flex justify-end pb-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 w-full max-w-lg">
                    <h3 class="text-base font-semibold text-gray-800 mb-4">{{ __('Tanda Tangan') }}</h3>
                    <div class="grid grid-cols-2 gap-4">

                        {{-- Pemohon --}}
                        <div class="flex flex-col items-center gap-2">
                            <span class="text-sm font-semibold text-gray-700">{{ __('Pemohon:') }}</span>

                            @if($upload->pemohon_photo)
                                <img src="{{ route('admin.ransum.photo.serve', [$upload->id, 'pemohon']) }}"
                                     alt="{{ __('Tanda tangan pemohon') }}"
                                     class="w-40 h-28 object-contain border border-gray-200 rounded-lg bg-gray-50">
                            @else
                                <div class="w-20 h-14 flex items-center justify-center border border-dashed border-gray-300 rounded-lg bg-gray-50 text-gray-400 text-xs text-center">
                                    {{ __('Belum ada foto') }}
                                </div>
                            @endif

                            <span class="text-xs text-gray-800 font-medium text-center">
                                {{ $upload->pemohon ?? '-' }}
                            </span>

                            <form method="POST"
                                  action="{{ route('admin.ransum.photo', [$upload->id, 'pemohon']) }}"
                                  enctype="multipart/form-data"
                                  class="w-full">
                                @csrf
                                <label class="block text-xs text-gray-500 mb-1">{{ __('Upload foto TTD pemohon') }}</label>
                                <div class="flex gap-1">
                                    <input type="file" name="photo" accept="image/*"
                                           class="block w-full text-xs text-gray-500 file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                    <button type="submit"
                                            class="shrink-0 px-2 py-1 bg-indigo-600 text-white text-xs font-semibold rounded hover:bg-indigo-700 transition">
                                        {{ __('Simpan') }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Menyetujui --}}
                        <div class="flex flex-col items-center gap-2">
                            <span class="text-sm font-semibold text-gray-700">{{ __('Menyetujui:') }}</span>

                            @if($upload->menyetujui_photo)
                                <img src="{{ route('admin.ransum.photo.serve', [$upload->id, 'menyetujui']) }}"
                                     alt="{{ __('Tanda tangan menyetujui') }}"
                                     class="w-40 h-28 object-contain border border-gray-200 rounded-lg bg-gray-50">
                            @else
                                <div class="w-20 h-14 flex items-center justify-center border border-dashed border-gray-300 rounded-lg bg-gray-50 text-gray-400 text-xs text-center">
                                    {{ __('Belum ada foto') }}
                                </div>
                            @endif

                            <span class="text-xs text-gray-800 font-medium text-center">
                                {{ $upload->menyetujui ?? '-' }}
                            </span>

                            <form method="POST"
                                  action="{{ route('admin.ransum.photo', [$upload->id, 'menyetujui']) }}"
                                  enctype="multipart/form-data"
                                  class="w-full">
                                @csrf
                                <label class="block text-xs text-gray-500 mb-1">{{ __('Upload foto TTD menyetujui') }}</label>
                                <div class="flex gap-1">
                                    <input type="file" name="photo" accept="image/*"
                                           class="block w-full text-xs text-gray-500 file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                    <button type="submit"
                                            class="shrink-0 px-2 py-1 bg-indigo-600 text-white text-xs font-semibold rounded hover:bg-indigo-700 transition">
                                        {{ __('Simpan') }}
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Bottom import button (repeated for long pages) -->
            @if($upload->status === 'pending' && count($sections) > 0)
                <div class="flex justify-end pb-4">
                    <form method="POST" action="{{ route('admin.ransum.import', $upload->id) }}"
                          onsubmit="return confirm('{{ __('Import semua data dari file ini ke database?') }}')">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            {{ __('Import ke Database') }}
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
