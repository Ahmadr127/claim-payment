@extends('layouts.app')

@section('title', 'Daftar Tarif Umum')

@section('content')
<div class="w-full mx-auto pb-8" x-data="createDiagnosisForm(0)">
    {{-- Breadcrumb & Title --}}
    <div class="mb-6">
        <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="hover:text-green-800 transition-colors">Dashboard</a>
                </li>
                <li>
                    <span class="mx-1 text-gray-400">/</span>
                </li>
                <li aria-current="page">
                    <span class="text-gray-700 font-medium">Daftar Diagnosa & Tarif Umum</span>
                </li>
            </ol>
        </nav>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Daftar Diagnosa & Tarif Umum</h2>
    </div>

    {{-- Main Card --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Toolbar / Filter Component --}}
        <x-table-filter 
            searchPlaceholder="Cari Kode/Nama Diagnosa..." 
            :showFilter="true" 
            :showExport="true">
            <button type="button" @click="openCreateModal = true" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-green-800 hover:bg-green-900 shadow-sm transition-colors cursor-pointer">
                <i class="fas fa-plus mr-2 text-xs"></i> Tambah Baru
            </button>
        </x-table-filter>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-slate-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left font-semibold text-gray-500 uppercase tracking-wider">NO</th>
                        <th scope="col" class="px-6 py-4 text-left font-semibold text-gray-500 uppercase tracking-wider">KODE ICD-10</th>
                        <th scope="col" class="px-6 py-4 text-left font-semibold text-gray-500 uppercase tracking-wider">NAMA DIAGNOSA</th>
                        <th scope="col" class="px-6 py-4 text-center font-semibold text-gray-500 uppercase tracking-wider">LAMA RAWAT</th>
                        <th scope="col" class="px-6 py-4 text-center font-semibold text-gray-500 uppercase tracking-wider">JUMLAH ADMIN</th>
                        <th scope="col" class="px-6 py-4 text-left font-semibold text-gray-500 uppercase tracking-wider">Dibuat</th>
                        <th scope="col" class="px-6 py-4 text-left font-semibold text-gray-500 uppercase tracking-wider">Diubah</th>
                        <th scope="col" class="px-6 py-4 text-center font-semibold text-gray-500 uppercase tracking-wider">STATUS TARIF</th>
                        <th scope="col" class="px-6 py-4 text-right font-semibold text-gray-500 uppercase tracking-wider">AKSI</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($diagnoses as $index => $diagnosis)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $diagnoses->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $diagnosis->icd_code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                <div class="font-medium text-gray-900">{{ $diagnosis->name }}</div>
                                <div class="mt-1 flex flex-wrap gap-1.5">
                                    @if($diagnosis->unitCostAssignments->count() > 0)
                                        @foreach($diagnosis->unitCostAssignments as $assignment)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                <i class="fas fa-chart-line mr-1 text-[9px]"></i> Unit Cost: {{ $assignment->organizationUnit?->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-200">
                                            <i class="fas fa-globe mr-1 text-[9px]"></i> Global (Tarif Umum)
                                        </span>
                                    @endif
                                </div>
                            </td>
                            @php
                                $pathway = \App\Models\ClinicalPathway\DiagnosisPathway::where('diagnosis_id', $diagnosis->id)->first();
                            @endphp
                            <td class="px-6 py-4 whitespace-nowrap text-center text-gray-600">
                                {{ $pathway ? $pathway->length_of_stay . ' Hari' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-gray-600">
                                {{ $diagnosis->admin_fee_percentage ? (float)$diagnosis->admin_fee_percentage . '%' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                <div class="font-medium text-gray-700">{{ $diagnosis->creator->name ?? 'System' }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $diagnosis->created_at ? $diagnosis->created_at->format('d/m/Y H:i') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                @if($diagnosis->updated_at && $diagnosis->updated_at != $diagnosis->created_at)
                                    <div class="font-medium text-gray-700">{{ $diagnosis->editor->name ?? 'System' }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $diagnosis->updated_at->format('d/m/Y H:i') }}</div>
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($pathway)
                                    <span class="px-2.5 py-1 inline-flex text-[11px] leading-4 font-semibold rounded-full bg-green-100 text-green-700">
                                        Tersedia
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-[11px] leading-4 font-semibold rounded-full bg-gray-100 text-gray-600">
                                        Belum Diatur
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($pathway)
                                        <a href="{{ route('diagnoses.pathway', $diagnosis) }}" class="inline-flex items-center px-2.5 py-1.5 border border-gray-200 rounded text-xs font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                            <i class="fas fa-eye mr-1.5 text-gray-400"></i> Lihat
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-sm">Data diagnosa tidak ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($diagnoses->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-white">
            {{ $diagnoses->links() }}
        </div>
        @else
        <div class="px-6 py-4 border-t border-gray-200 bg-white text-sm text-gray-500 flex justify-between items-center">
            <div>
                Showing 1 to {{ $diagnoses->count() }} of {{ $diagnoses->total() }} entries
            </div>
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
                    Diagnosa baru telah berhasil disimpan ke database. Apakah Anda ingin langsung menyusun Clinical Pathway (menambah layanan)?
                </p>

                <div class="flex flex-col gap-2">
                    <a :href="pathwayUrl" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg text-sm shadow-sm transition-all text-center block">
                        Ya, Susun Pathway
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
