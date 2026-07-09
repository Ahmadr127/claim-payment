@extends('layouts.app')

@section('title', 'Unit Cost Obat & Alkes')

@section('content')
<div class="w-full mx-auto pb-10">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between space-y-4 md:space-y-0">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <span class="text-gray-500">Unit Cost</span>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-xs mx-2"></i>
                            <span class="text-gray-800 font-medium">Tarif & Harga Obat (UC)</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Tarif & Harga Obat (Unit Cost)</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data tarif unit cost (HNA & PPN) obat atau alkes per kelas kamar</p>
        </div>
        <!-- Rumus Unit Cost di Kanan Atas -->
        <div class="bg-teal-50 border border-teal-200 text-teal-900 px-3 py-2 rounded-lg text-xs shadow-sm flex items-center gap-2 self-start md:self-auto">
            <i class="fas fa-info-circle text-teal-600"></i>
            <span><strong>Rumus Unit Cost:</strong> HNA + PPN = Tarif UC Akhir</span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-start">
            <i class="fas fa-check-circle mt-0.5 mr-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-start">
            <i class="fas fa-exclamation-circle mt-0.5 mr-3"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Toolbar -->
    <div class="bg-white p-4 rounded-t-lg border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
        <form action="{{ route('unit-cost-medication-prices.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4">
            
            <div class="w-full md:w-1/3">
                <select name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" onchange="this.form.submit()">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="relative w-full md:w-96 flex">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari kode barang atau nama obat..." 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm transition-colors">
            </div>
            
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg border border-gray-300 transition-colors">Cari</button>
            <a href="{{ route('unit-cost-medication-prices.index') }}" class="bg-white hover:bg-gray-50 text-gray-500 px-4 py-2 rounded-lg border border-gray-300 transition-colors text-center">Reset</a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-b-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm border-collapse">
                <thead class="bg-slate-50 border-b border-gray-200">
                    <tr>
                        <th rowspan="2" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16 border-r border-gray-200">No</th>
                        <th rowspan="2" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24 border-r border-gray-200">Kode</th>
                        <th rowspan="2" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-200">Nama Obat / Alkes</th>
                        <th rowspan="2" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-36 border-r border-gray-200">Kategori</th>
                        <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20 border-r border-gray-200">Satuan</th>
                        <th rowspan="2" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-24 border-r border-gray-200">HNA</th>
                        <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20 border-r border-gray-200">PPN</th>
                        <th colspan="{{ $roomClasses->count() }}" class="px-6 py-2 text-center text-xs font-bold text-teal-800 uppercase tracking-wider border-b border-gray-200">Tarif Unit Cost per Kelas (Rp)</th>
                        <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20 border-l border-gray-200">Status</th>
                        <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-28 border-l border-gray-200">Aksi</th>
                    </tr>
                    <tr class="bg-slate-50/50">
                        @foreach($roomClasses as $rc)
                            <th class="px-3 py-2 text-center text-[10px] font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-200 last:border-r-0">{{ $rc->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($medications as $index => $med)
                        @php
                            $hna = $med->hna ?? 0;
                            $ppn = $med->ppn_percentage ?? 11.00;
                            $ucAmount = (int) round($hna * (1 + ($ppn / 100)));
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-4 text-gray-500 border-r border-gray-100 text-center">{{ $medications->firstItem() + $index }}</td>
                            <td class="px-4 py-4 text-gray-900 font-medium border-r border-gray-100 text-center font-mono">{{ $med->item_code }}</td>
                            <td class="px-6 py-4 text-gray-900 font-semibold border-r border-gray-100">{{ $med->name }}</td>
                            <td class="px-6 py-4 text-gray-700 border-r border-gray-100">{{ $med->medicationCategory->name ?? '-' }}</td>
                            <td class="px-4 py-4 text-center text-gray-500 border-r border-gray-100">{{ $med->unit }}</td>
                            <td class="px-4 py-4 text-right font-medium text-gray-900 border-r border-gray-100">Rp{{ number_format($hna, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-center text-gray-700 border-r border-gray-100">{{ number_format($ppn, 1) }}%</td>
                            @foreach($roomClasses as $rc)
                                <td class="px-3 py-4 text-right font-medium text-teal-700 border-r border-gray-100 bg-teal-50/10">
                                    @if($ucAmount > 0)
                                        <span class="font-bold">Rp{{ number_format($ucAmount, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-400 italic text-xs block text-center">-</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-4 py-4 text-center border-l border-gray-100">
                                @if($med->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center border-l border-gray-100">
                                <a href="{{ route('unit-cost-medication-prices.edit', $med->id) }}" class="text-teal-600 hover:text-teal-900 font-medium inline-flex items-center gap-1.5 transition-colors text-xs" title="Kelola Tarif Unit Cost">
                                    <i class="fas fa-edit"></i> Edit UC
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 9 + $roomClasses->count() }}" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                <p>Tidak ada data obat / alkes.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($medications->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $medications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
