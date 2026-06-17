{{-- _toolbar.blade.php --}}
<div class="flex flex-col lg:flex-row gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 lg:w-80 flex items-center px-4">
        <i class="fas fa-search text-gray-400"></i>
        <input
            type="text"
            x-model="searchQuery"
            placeholder="Cari nama/kode layanan..."
            class="w-full border-none outline-none focus:outline-none focus:ring-0 text-sm ml-2 py-3 bg-transparent"
        >
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-2 flex overflow-x-auto flex-1">
        @foreach($roomClasses as $rc)
        <button
            type="button"
            @click="activeTab = {{ $rc->id }}"
            class="flex-1 py-3 px-6 text-sm font-bold uppercase tracking-wider rounded-md transition-colors whitespace-nowrap"
            :class="activeTab === {{ $rc->id }} ? 'bg-green-800 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
        >
            {{ $rc->code }}
        </button>
        @endforeach
    </div>
</div>
