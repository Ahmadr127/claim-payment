@extends('layouts.app')

@section('title', 'Unit Cost - Simulasi Biaya Tindakan')

@section('content')
<div class="w-full mx-auto pb-10" x-data="createDiagnosisForm(1)">

    {{-- Simple Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Unit Cost - Simulasi Biaya Tindakan</h1>
        <p class="text-sm text-gray-500 mt-1">Unit Organisasi: <span class="font-medium text-gray-700">{{ $organizationUnit->name }}</span></p>
    </div>

    {{-- Info Alert --}}
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-3">
        <i class="fas fa-info-circle text-blue-600 mt-0.5 flex-shrink-0"></i>
        <div class="text-sm text-blue-800">
            <strong>Catatan:</strong> Unit Cost menampilkan simulasi biaya tindakan medis berdasarkan <strong>HNA (Harga Netto Apotek) + PPN</strong> untuk obat/alkes, 
            sedangkan layanan medis dan kamar menggunakan tarif yang berlaku. Data ini membantu Anda memahami cost breakdown per tindakan untuk manajemen unit.
        </div>
    </div>

    {{-- Container Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- Search and Filter Toolbar --}}
        <div class="bg-slate-50 p-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <form action="{{ route('unit-cost.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="relative w-full md:w-96 flex">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari kode ICD atau nama diagnosa..." class="block w-full pl-10 pr-3 py-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>
                <div class="flex gap-2 w-full md:w-auto justify-end">
                    @if(!empty($search))
                        <a href="{{ route('unit-cost.index') }}" class="bg-gray-150 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition-colors border border-gray-300 flex items-center gap-1">
                            <i class="fas fa-times"></i> Bersihkan
                        </a>
                    @endif
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center gap-1.5">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button type="button" @click="openCreateModal = true" class="bg-green-800 hover:bg-green-900 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-plus text-xs"></i> Tambah Baru
                    </button>
                </div>
            </form>
        </div>

        @if($assignments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Kode ICD</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Diagnosa</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-64">Dibuat Oleh</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-64">Diupdate Oleh</th>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($assignments as $assignment)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-center text-sm font-medium text-gray-500">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono font-medium text-gray-900 bg-gray-100 px-2 py-1 rounded text-xs border border-gray-200">
                                        {{ $assignment->diagnosis->icd_code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 text-sm">{{ $assignment->diagnosis->name }}</div>
                                    <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-2">
                                        @if($assignment->diagnosis->pathway)
                                            <span class="inline-flex items-center px-2 py-0.5 bg-green-50 text-green-700 rounded border border-green-100 text-xs font-medium">
                                                <i class="fas fa-check-circle mr-1"></i> Ada Pathway
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 bg-yellow-50 text-yellow-700 rounded border border-yellow-100 text-xs font-medium">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Belum Ada Pathway
                                            </span>
                                        @endif

                                        @if($assignment->is_customized)
                                            <span class="inline-flex items-center px-2 py-0.5 bg-blue-50 text-blue-700 rounded border border-blue-100 text-xs font-medium">
                                                <i class="fas fa-user-edit mr-1"></i> Kustom
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-600 rounded border border-gray-200 text-xs font-medium">
                                                <i class="fas fa-file-invoice mr-1"></i> Standar
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($assignment->assignedBy)
                                        <div class="font-medium text-gray-800">{{ $assignment->assignedBy->name }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            {{ $assignment->assigned_at ? $assignment->assigned_at->format('d M Y H:i') : '-' }}
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($assignment->is_customized && $assignment->customizedBy)
                                        <div class="font-medium text-gray-800">{{ $assignment->customizedBy->name }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            {{ $assignment->customized_at ? $assignment->customized_at->format('d M Y H:i') : '-' }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 font-normal italic">Belum diupdate</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($assignment->diagnosis->pathway)
                                        <a href="{{ route('unit-cost.simulation.show', [$organizationUnit, $assignment->diagnosis]) }}"
                                           class="inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-all duration-150 active:scale-95">
                                            <i class="fas fa-eye text-[10px]"></i> Lihat
                                        </a>
                                    @else
                                        <span class="inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-400 border border-gray-200 rounded-lg text-xs font-semibold cursor-not-allowed">
                                            <i class="fas fa-lock text-[10px]"></i> Kunci
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-lg"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1">Tidak Ada Hasil</h3>
                <p class="text-gray-500 text-xs">Diagnosa pencarian Anda tidak ditemukan atau belum ditugaskan.</p>
            </div>
        @endif
    </div>

    <!-- Modal Diagnosis -->
    <div x-show="openCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="openCreateModal = false"></div>

        <!-- Modal Content -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden max-w-md w-full transition-all transform" @click.away="openCreateModal = false">
                
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-150 flex items-center justify-between bg-slate-50">
                    <h3 class="text-base font-bold text-gray-900">Tambah Diagnosa Baru</h3>
                    <button type="button" @click="openCreateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitForm()" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Kode ICD-10 <span class="text-red-500">*</span></label>
                        <input type="text" x-model="icd_code" placeholder="Contoh: K81.9" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500" :class="errors.icd_code ? 'border-red-500 focus:ring-red-500' : ''" required>
                        <p class="text-red-500 text-xs mt-1" x-show="errors.icd_code" x-text="errors.icd_code"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Nama Diagnosa <span class="text-red-500">*</span></label>
                        <input type="text" x-model="name" placeholder="Contoh: Cholecystitis" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500" :class="errors.name ? 'border-red-500 focus:ring-red-500' : ''" required>
                        <p class="text-red-500 text-xs mt-1" x-show="errors.name" x-text="errors.name"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Jumlah Admin (%)</label>
                        <input type="number" step="0.01" x-model="admin_fee_percentage" placeholder="Contoh: 1.5" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500" :class="errors.admin_fee_percentage ? 'border-red-500 focus:ring-red-500' : ''">
                        <p class="text-red-500 text-xs mt-1" x-show="errors.admin_fee_percentage" x-text="errors.admin_fee_percentage"></p>
                    </div>

                    <div class="pt-4 flex justify-end gap-2 border-t border-gray-150">
                        <button type="button" @click="openCreateModal = false" class="px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-1.5" :disabled="submitting">
                            <i class="fas fa-spinner fa-spin" x-show="submitting"></i>
                            Simpan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Success Alert Modal -->
    <div x-show="showSuccessAlert" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden max-w-sm w-full p-6 text-center transition-all transform">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-xl"></i>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 mb-2">Berhasil Dibuat!</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Diagnosa baru telah berhasil disimpan ke database. Apakah Anda ingin langsung menyusun Unit Cost (menambah layanan)?
                </p>

                <div class="flex flex-col gap-2">
                    <a :href="unitCostUrl" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg text-sm shadow-sm transition-all text-center block">
                        Ya, Susun Unit Cost
                    </a>
                    <button type="button" @click="window.location.reload()" class="w-full py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-all">
                        Tidak, Kembali ke Daftar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function createDiagnosisForm(assignToUnit = 0) {
        return {
            openCreateModal: false,
            showSuccessAlert: false,
            name: '',
            icd_code: '',
            admin_fee_percentage: '',
            errors: {},
            submitting: false,
            pathwayUrl: '',
            unitCostUrl: '',
            submitForm() {
                this.submitting = true;
                this.errors = {};
                
                axios.post('{{ route("diagnoses.pathway.store") }}', {
                    name: this.name,
                    icd_code: this.icd_code,
                    admin_fee_percentage: this.admin_fee_percentage,
                    assign_to_unit: assignToUnit
                })
                .then(response => {
                    this.submitting = false;
                    if (response.data.success) {
                        this.pathwayUrl = response.data.redirect;
                        this.unitCostUrl = response.data.unit_cost_redirect;
                        this.openCreateModal = false;
                        this.showSuccessAlert = true;
                    } else {
                        alert(response.data.message || 'Terjadi kesalahan.');
                    }
                })
                .catch(error => {
                    this.submitting = false;
                    if (error.response && error.response.status === 422) {
                        const errs = error.response.data.errors;
                        for (const key in errs) {
                            this.errors[key] = errs[key][0];
                        }
                    } else {
                        alert('Terjadi kesalahan koneksi atau server.');
                    }
                });
            }
        };
    }
</script>
@endpush
@endsection
