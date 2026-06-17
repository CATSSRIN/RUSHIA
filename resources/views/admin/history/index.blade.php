<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Histori Aktivitas Admin') }}
            </h2>
            <span class="text-xs font-semibold bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-xl border border-indigo-200 shadow-sm">
                {{ __('Total Logs: ') }}{{ $logs->total() }}
            </span>
        </div>
    </x-slot>

    @php
        function getActionColor($action) {
            if (str_contains($action, 'create') || str_contains($action, 'store') || str_contains($action, 'add') || str_contains($action, 'upload') || str_contains($action, 'import')) {
                return 'bg-emerald-50 text-emerald-700 border-emerald-200';
            }
            if (str_contains($action, 'delete') || str_contains($action, 'destroy') || str_contains($action, 'remove')) {
                return 'bg-rose-50 text-rose-700 border-rose-200';
            }
            if (str_contains($action, 'download') || str_contains($action, 'export') || str_contains($action, 'pdf')) {
                return 'bg-sky-50 text-sky-700 border-sky-200';
            }
            if (str_contains($action, 'update') || str_contains($action, 'edit') || str_contains($action, 'status') || str_contains($action, 'finalize')) {
                return 'bg-amber-50 text-amber-700 border-amber-200';
            }
            return 'bg-gray-50 text-gray-700 border-gray-200';
        }

        function getActionIcon($action) {
            if (str_contains($action, 'create') || str_contains($action, 'store') || str_contains($action, 'add')) {
                return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>';
            }
            if (str_contains($action, 'upload') || str_contains($action, 'import')) {
                return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>';
            }
            if (str_contains($action, 'delete') || str_contains($action, 'destroy') || str_contains($action, 'remove')) {
                return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
            }
            if (str_contains($action, 'download') || str_contains($action, 'export') || str_contains($action, 'pdf')) {
                return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>';
            }
            if (str_contains($action, 'update') || str_contains($action, 'edit') || str_contains($action, 'status') || str_contains($action, 'finalize')) {
                return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
            }
            return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        }
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Search and Filter Pane -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <form method="GET" action="{{ route('admin.history.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- User Filter -->
                        <div>
                            <label for="user_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Admin / User</label>
                            <select id="user_id" name="user_id" class="w-full text-sm text-gray-700 border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                                <option value="">Semua Akun</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->is_admin ? 'Admin' : ($user->is_warehouse ? 'Gudang' : 'User') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Filter -->
                        <div>
                            <label for="action" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Aksi / Kegiatan</label>
                            <select id="action" name="action" class="w-full text-sm text-gray-700 border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                                <option value="">Semua Kategori</option>
                                @foreach($actionTypes as $type)
                                    <option value="{{ $type }}" {{ request('action') == $type ? 'selected' : '' }}>
                                        {{ $actionLabels[$type] ?? $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Start -->
                        <div>
                            <label for="date_start" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                            <input type="date" id="date_start" name="date_start" value="{{ request('date_start') }}" class="w-full text-sm text-gray-700 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                        </div>

                        <!-- Date End -->
                        <div>
                            <label for="date_end" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tanggal Selesai</label>
                            <input type="date" id="date_end" name="date_end" value="{{ request('date_end') }}" class="w-full text-sm text-gray-700 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                        </div>

                        <!-- Keyword Search -->
                        <div>
                            <label for="search" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pencarian Kata Kunci</label>
                            <div class="relative">
                                <input type="text" id="search" name="search" placeholder="Cari IP, deskripsi..." value="{{ request('search') }}" class="w-full text-sm text-gray-700 border border-gray-300 rounded-lg pl-9 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Action Buttons -->
                    <div class="flex justify-end gap-2 border-t border-gray-100 pt-4 mt-2">
                        <a href="{{ route('admin.history.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition shadow-sm">
                            {{ __('Reset Filter') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-5 py-2 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700 transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            {{ __('Terapkan Filter') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Activity Logs List / Timeline -->
            @if($logs->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
                    <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-400 font-medium">{{ __('Tidak ada riwayat aktivitas yang ditemukan.') }}</p>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="min-w-full divide-y divide-gray-200 block md:table">
                        <div class="bg-gray-50 hidden md:table-header-group">
                            <div class="md:table-row">
                                <div class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider md:table-cell w-1/4">Nama Akun & Email</div>
                                <div class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider md:table-cell w-1/6">Aktivitas</div>
                                <div class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider md:table-cell w-2/5">Deskripsi Tindakan</div>
                                <div class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider md:table-cell w-1/6">Waktu & IP Address</div>
                            </div>
                        </div>
                        <div class="bg-white divide-y divide-gray-100 block md:table-row-group">
                            @foreach($logs as $log)
                                <div class="hover:bg-slate-50 transition block md:table-row">
                                    
                                    <!-- User Column (Prominent name verification check) -->
                                    <div class="px-6 py-4 whitespace-nowrap block md:table-cell">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                                {{ $log->user ? strtoupper(substr($log->user->name, 0, 2)) : 'SYS' }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-800">
                                                    {{ $log->user ? $log->user->name : 'System / Deleted User' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $log->user ? $log->user->email : '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Badge Column -->
                                    <div class="px-6 py-4 whitespace-nowrap block md:table-cell">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border shadow-xs {{ getActionColor($log->action) }}">
                                            {!! getActionIcon($log->action) !!}
                                            {{ $actionLabels[$log->action] ?? $log->action }}
                                        </span>
                                    </div>

                                    <!-- Description Column -->
                                    <div class="px-6 py-4 block md:table-cell">
                                        <div class="text-sm text-gray-700 leading-relaxed font-medium">
                                            {{ $log->description }}
                                        </div>
                                    </div>

                                    <!-- IP & Time Column -->
                                    <div class="px-6 py-4 whitespace-nowrap block md:table-cell text-sm text-gray-500">
                                        <div class="flex flex-col gap-1 justify-start">
                                            <div class="flex items-center gap-1 text-gray-800 font-semibold">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                            </div>
                                            <div class="inline-flex items-center gap-1 text-xs font-mono bg-gray-100 text-gray-600 px-2 py-0.5 rounded w-fit">
                                                IP: {{ $log->ip_address ?? '127.0.0.1' }}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pagination Footer -->
                    @if($logs->hasPages())
                        <div class="bg-white px-6 py-4 border-t border-gray-200">
                            {{ $logs->links() }}
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
