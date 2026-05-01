<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Buat Delivery Order (DO)') }}: {{ $upload->vessel_name ?? $upload->original_filename }}
            </h2>
            <a href="{{ route('admin.ransum.preview', $upload->id) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                &larr; {{ __('Kembali ke Preview') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <form method="POST" action="{{ route('admin.ransum.do.download', $upload->id) }}">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Nomor DO *</label>
                        <input type="text" name="no_do" value="{{ $upload->no_do ?? '009/DO-AMS-LBJ/III/2026' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">PO Number</label>
                        <input type="text" name="po_number" value="{{ $upload->po_number ?? $upload->po_number ?? '000' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Request Date</label>
                        <input type="date" name="request_date" value="{{ $upload->request_date ?? now()->subDays(4)->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Delivery Date</label>
                        <input type="date" name="delivery_date" value="{{ $upload->delivery_date ?? now()->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Deliver To</label>
                        <input type="text" name="deliver_to" value="{{ $upload->deliver_to ?? strtoupper($upload->vessel_name) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Captain</label>
                        <input type="text" name="captain" value="{{ $upload->captain ?? 'Capt. Agus' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">ETB JKT</label>
                        <input type="text" name="etb_jkt" value="{{ $upload->etb_jkt ?? $upload->eta }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
                
                <div class="flex justify-end border-t pt-4">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                        {{ __('Simpan & Download PDF DO') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>