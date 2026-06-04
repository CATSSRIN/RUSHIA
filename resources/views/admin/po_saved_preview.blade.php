<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-1">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Preview Purchase Order - {{ $poNumber }}
                </h2>
                <p class="text-sm text-gray-500"> Dokumen PO yang telah tersimpan di sistem.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ $backUrl }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Kembali
                </a>
                <a href="{{ $downloadUrl }}" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 shadow-sm gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <object data="{{ $streamUrl }}" type="application/pdf" class="w-full border-none block" style="height: calc(100vh - 150px); min-height: 900px;">
                    <div class="p-8 text-center bg-gray-50 text-gray-500 rounded-2xl">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-base font-semibold text-gray-700 mb-1">Browser tidak mendukung preview PDF langsung</p>
                        <p class="text-sm text-gray-400 mb-4">Silakan klik tombol di bawah untuk mengunduh dokumen secara manual.</p>
                        <a href="{{ $downloadUrl }}" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 shadow-sm gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download PDF
                        </a>
                    </div>
                </object>
            </div>
        </div>
    </div>
</x-app-layout>
