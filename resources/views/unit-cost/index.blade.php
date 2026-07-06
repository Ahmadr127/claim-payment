@extends('layouts.app')

@section('title', 'Unit Cost - Simulasi Biaya Tindakan')

@section('content')
<div class="w-full mx-auto pb-10">

    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2">
                    <li>
                        <a href="{{ route('dashboard') }}" class="hover:text-green-800 transition-colors">Dashboard</a>
                    </li>
                    <li>
                        <span class="mx-1 text-gray-400">/</span>
                    </li>
                    <li aria-current="page">
                        <span class="text-gray-700 font-medium">Unit Cost</span>
                    </li>
                </ol>
            </nav>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Unit Cost - Simulasi Biaya Tindakan</h1>
                <p class="text-sm text-gray-500 mt-1">Unit Organisasi: <span class="font-medium text-gray-700">{{ $organizationUnit->name }}</span></p>
                <p class="text-sm text-gray-500">Simulasi biaya berdasarkan HNA + PPN untuk obat/alkes dan tarif standar untuk layanan medis</p>
            </div>
        </div>
    </div>

    {{-- Info Alert --}}
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-3">
        <i class="fas fa-info-circle text-blue-600 mt-0.5 flex-shrink-0"></i>
        <div class="text-sm text-blue-800">
            <strong>Catatan:</strong> Unit Cost menampilkan simulasi biaya tindakan medis berdasarkan <strong>HNA (Harga Netto Apotek) + PPN</strong> untuk obat/alkes, 
            sedangkan layanan medis dan kamar menggunakan tarif yang berlaku. Data ini membantu Anda memahami cost breakdown per tindakan untuk manajemen unit.
        </div>
    </div>

    {{-- Assigned Diagnoses List --}}
    @if($assignments->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-slate-50">
                <h2 class="text-lg font-semibold text-gray-800">Daftar Diagnosa Ditugaskan ke Unit Anda</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $assignments->count() }} diagnosa tersedia untuk simulasi</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode ICD</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Diagnosa</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catatan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ditugaskan Oleh</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Penugasan</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($assignments as $assignment)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-mono font-medium text-gray-900">{{ $assignment->diagnosis->icd_code }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $assignment->diagnosis->name }}</div>
                                    <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-2">
                                        @if($assignment->diagnosis->pathway)
                                            <span class="inline-flex items-center px-2 py-0.5 bg-green-50 text-green-700 rounded text-xs">
                                                <i class="fas fa-check-circle mr-1"></i> Ada Pathway
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 bg-yellow-50 text-yellow-700 rounded text-xs">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Belum Ada Pathway
                                            </span>
                                        @endif

                                        @if($assignment->is_customized)
                                            <span class="inline-flex items-center px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs">
                                                <i class="fas fa-user-edit mr-1"></i> Kustom
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                                <i class="fas fa-file-invoice mr-1"></i> Standar
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $assignment->notes ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $assignment->assignedBy?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($assignment->assigned_at)
                                        {{ $assignment->assigned_at->format('d M Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($assignment->diagnosis->pathway)
                                        <a href="{{ route('unit-cost.simulation.show', [$organizationUnit, $assignment->diagnosis]) }}"
                                           class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition-colors text-sm font-medium"
                                           title="Lihat Simulasi">
                                            <i class="fas fa-eye mr-1"></i> Lihat
                                        </a>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-500 rounded-md text-sm font-medium cursor-not-allowed">
                                            <i class="fas fa-lock mr-1"></i> Tidak Tersedia
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Diagnosa</h3>
            <p class="text-gray-600">Unit organisasi Anda belum memiliki diagnosa yang ditugaskan untuk simulasi Unit Cost.</p>
            <p class="text-sm text-gray-500 mt-2">Hubungi administrator untuk menambahkan diagnosa ke unit Anda.</p>
        </div>
    @endif

</div>
@endsection
