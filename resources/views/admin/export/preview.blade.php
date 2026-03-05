<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Preview File') }}: {{ $safeName }}</h2>
            <a href="{{ route('admin.export.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($extension === 'csv')
                @if(empty($rows))
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-400">{{ __('The file is empty.') }}</p>
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">{{ __('CSV Contents') }}</h3>
                            <div class="flex items-center gap-3">
                                @if($truncated)
                                    <span class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded px-2 py-1">{{ __('Showing first 1,000 rows') }}</span>
                                @endif
                                <span class="text-sm text-gray-400">{{ count($rows) }} {{ __('row(s)') }}</span>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @foreach($rows[0] as $heading)
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">
                                                {{ $heading }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach(array_slice($rows, 1) as $row)
                                        <tr class="hover:bg-gray-50">
                                            @foreach($row as $cell)
                                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="mx-auto w-14 h-14 text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-600 font-medium mb-1">{{ strtoupper($extension) }} {{ __('files cannot be previewed in the browser.') }}</p>
                    <p class="text-gray-400 text-sm mb-6">{{ __('Please download the file to view its contents.') }}</p>
                    <a href="{{ route('admin.export.download', $safeName) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        {{ __('Download') }} {{ $safeName }}
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
