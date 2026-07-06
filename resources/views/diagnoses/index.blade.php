@extends('layouts.app')

@section('title', 'Daftar Tarif Umum')

@section('content')
<div class="w-full mx-auto pb-8">
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
            :showExport="true" 
            addAction="{{ route('diagnoses.pathway.create') }}" />

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
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $diagnosis->name }}</td>
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
</div>
@endsection
