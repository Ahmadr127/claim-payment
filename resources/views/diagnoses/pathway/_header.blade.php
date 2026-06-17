{{-- _header.blade.php --}}
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
    <div>
        <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li>
                    <a href="{{ route('diagnoses.index') }}" wire:navigate class="hover:text-green-800 transition-colors">Daftar Diagnosa &amp; Tarif Umum</a>
                </li>
                <li>
                    <span class="mx-1 text-gray-400">/</span>
                </li>
                <li aria-current="page">
                    <span class="text-gray-700 font-medium">Simulasi Tarif Umum</span>
                </li>
            </ol>
        </nav>
        <h2 class="text-2xl font-bold text-gray-900">Tarif Umum: {{ $diagnosis->name }}</h2>
        <p class="text-sm text-gray-500 mt-1">Kode ICD-10: {{ $diagnosis->icd_code }} &nbsp;|&nbsp; Lama rawat: <span x-text="getKamarQty()"></span> hari</p>
    </div>

    {{-- Tombol Tambah Layanan --}}
    <div>
        <button
            type="button"
            @click="$dispatch('open-add-service-modal')"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-lg font-semibold text-sm shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
        >
            <i class="fas fa-plus-circle"></i>
            Tambah Layanan
        </button>
    </div>
</div>
