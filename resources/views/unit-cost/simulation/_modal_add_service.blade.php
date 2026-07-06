{{-- _modal_add_service.blade.php --}}
<div
    x-data="addServiceModal()"
    @open-add-service-modal.window="openModal()"
    @keydown.escape.window="closeModal()"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
        @click="closeModal()"
        x-cloak
    ></div>

    {{-- Modal Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="fixed inset-0 z-50 flex items-start justify-center pt-20 px-4"
        x-cloak
    >
        <div
            class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-200"
            @click.stop
        >
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-plus text-green-700 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Tambah Layanan</h3>
                        <p class="text-xs text-gray-500">Cari tindakan, obat, alkes, atau kamar dari database</p>
                    </div>
                </div>
                <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Search Input --}}
            <div class="px-6 pt-4 pb-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm" x-show="!loading"></i>
                        <svg x-show="loading" class="animate-spin h-4 w-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                    <input
                        type="text"
                        x-model="searchQuery"
                        x-ref="searchInput"
                        @input.debounce.300ms="fetchServices()"
                        @keydown.arrow-down.prevent="moveFocus(1)"
                        @keydown.arrow-up.prevent="moveFocus(-1)"
                        @keydown.enter.prevent="selectFocused()"
                        placeholder="Ketik nama atau kode layanan (min. 2 karakter)..."
                        autocomplete="off"
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                    >
                </div>
            </div>

            {{-- Results List --}}
            <div class="px-6 pb-4">
                {{-- Hint --}}
                <div x-show="searchQuery.length === 0 && results.length === 0"
                     class="py-8 text-center text-gray-400">
                    <i class="fas fa-magnifying-glass text-3xl mb-3 block opacity-40"></i>
                    <p class="text-sm">Mulai mengetik untuk mencari layanan</p>
                    <p class="text-xs mt-1 text-gray-400">Jasa Medis, Obat &amp; Alkes, Tarif Kamar</p>
                </div>

                {{-- Min length hint --}}
                <div x-show="searchQuery.length > 0 && searchQuery.length < 2 && !loading"
                     class="py-6 text-center text-gray-400">
                    <p class="text-sm">Ketik minimal 2 karakter...</p>
                </div>

                {{-- No results --}}
                <div x-show="searchQuery.length >= 2 && results.length === 0 && !loading"
                     class="py-6 text-center text-gray-400">
                    <i class="fas fa-inbox text-2xl mb-2 block opacity-40"></i>
                    <p class="text-sm">Tidak ada layanan ditemukan untuk "<span x-text="searchQuery" class="font-medium text-gray-600"></span>"</p>
                </div>

                {{-- Results --}}
                <div x-show="results.length > 0"
                     class="mt-2 border border-gray-200 rounded-lg overflow-hidden max-h-72 overflow-y-auto divide-y divide-gray-100">
                    <template x-for="(service, idx) in results" :key="service.type + '_' + service.id">
                        <div
                            :id="'result-item-' + idx"
                            @click="if (!service.is_added) selectService(service)"
                            @mouseenter="if (!service.is_added) focusedIndex = idx"
                            :class="{
                                'bg-green-50 border-l-2 border-l-green-500 cursor-pointer': focusedIndex === idx && !service.is_added,
                                'bg-white hover:bg-gray-50 cursor-pointer': focusedIndex !== idx && !service.is_added,
                                'bg-gray-50 opacity-60 cursor-not-allowed': service.is_added
                            }"
                            class="flex items-center justify-between px-4 py-3 transition-colors border-l-2 border-l-transparent"
                        >
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate" x-text="service.name"></p>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="'Kode: ' + service.code"></p>
                            </div>
                            <div class="ml-3 flex-shrink-0 flex items-center gap-2">
                                <span x-show="service.is_added" class="text-xs font-semibold text-gray-500 bg-gray-200 px-2 py-0.5 rounded-full">Sudah Ada</span>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-emerald-100 text-emerald-700': service.type === 'MedicalService',
                                        'bg-blue-100 text-blue-700': service.type === 'Medication',
                                        'bg-violet-100 text-violet-700': service.type === 'RoomTariffType'
                                    }"
                                    x-text="service.type_label"
                                ></span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Keyboard hint --}}
                <div x-show="results.length > 0" class="mt-2 flex items-center gap-3 text-xs text-gray-400">
                    <span><kbd class="px-1 py-0.5 bg-gray-100 border border-gray-300 rounded text-gray-500">↑↓</kbd> Navigasi</span>
                    <span><kbd class="px-1 py-0.5 bg-gray-100 border border-gray-300 rounded text-gray-500">Enter</kbd> Pilih</span>
                    <span><kbd class="px-1 py-0.5 bg-gray-100 border border-gray-300 rounded text-gray-500">Esc</kbd> Tutup</span>
                </div>
            </div>
        </div>
    </div>
</div>
