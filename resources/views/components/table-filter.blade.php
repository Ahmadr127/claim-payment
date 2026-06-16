@props([
    'searchPlaceholder' => 'Cari...',
    'showFilter' => true,
    'showExport' => true,
    'addAction' => null
])

<div class="p-4 bg-white border-b border-gray-200">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <form action="{{ request()->url() }}" method="GET" class="relative w-full sm:w-80" id="searchForm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $searchPlaceholder }}"
                       onchange="document.getElementById('searchForm').submit()"
                       class="block w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg bg-slate-50 text-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
            </form>
            @if($showFilter)
            <button type="button" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                <i class="fas fa-filter mr-2 text-xs"></i> Filter
            </button>
            @endif
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto md:justify-end">
            @if($showExport)
            <button type="button" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                <i class="fas fa-download mr-2 text-xs"></i> Export
            </button>
            @endif
            
            {{ $slot }}
            
            @if($addAction)
            <a href="{{ $addAction }}" wire:navigate class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-green-800 hover:bg-green-900 shadow-sm transition-colors">
                <i class="fas fa-plus mr-2 text-xs"></i> Tambah Baru
            </a>
            @endif
        </div>
    </div>
</div>
