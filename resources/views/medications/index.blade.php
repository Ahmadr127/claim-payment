@extends('layouts.app')

@section('title', 'Master Data Obat & Alkes')

@section('content')
<div class="w-full mx-auto pb-10">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <span class="text-gray-500">Master Data</span>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-xs mx-2"></i>
                            <span class="text-gray-800 font-medium">Obat & Alkes</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Obat & Alat Kesehatan (Alkes)</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data obat-obatan, cairan infus, alat kesehatan, dan tarifnya per kelas kamar</p>
        </div>

        <a href="{{ route('medications.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg shadow-sm font-medium transition-colors flex items-center gap-2 w-max">
            <i class="fas fa-plus"></i> Tambah Obat/Alkes
        </a>
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
        <form action="{{ route('medications.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4">
            
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
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari kode atau nama item..." 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm transition-colors">
            </div>
            
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg border border-gray-300 transition-colors">Cari</button>
            <a href="{{ route('medications.index') }}" class="bg-white hover:bg-gray-50 text-gray-500 px-4 py-2 rounded-lg border border-gray-300 transition-colors text-center">Reset</a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-b-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Kode Item</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Obat / Alkes</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Golongan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">HNA</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Satuan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Dibuat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Diubah</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($medications as $index => $med)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">{{ $medications->firstItem() + $index }}</td>
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ $med->item_code }}</td>
                            <td class="px-6 py-4 text-gray-900">
                                <div class="font-semibold">{{ $med->name }}</div>
                                @if($med->active_ingredient)
                                    <div class="text-xs text-gray-500 mt-0.5">Zat Aktif: {{ $med->active_ingredient }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-900">
                                @if($med->group)
                                    <div class="font-medium text-gray-800">{{ $med->group->name }}</div>
                                    @if($med->group->code)
                                        <div class="text-xs text-gray-400">Golongan: {{ $med->group->code }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $med->medicationCategory->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($med->hna)
                                    <div class="font-semibold text-gray-900">Rp {{ number_format($med->hna, 0, ',', '.') }}</div>
                                    @if($med->hna_ppn)
                                        <div class="text-[10px] text-gray-400">PPN: Rp {{ number_format($med->hna_ppn, 0, ',', '.') }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500">{{ $med->unit }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($med->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                <div class="font-medium text-gray-700">{{ $med->creator->name ?? 'System' }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $med->created_at ? $med->created_at->format('d/m/Y H:i') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                @if($med->updated_at && $med->updated_at != $med->created_at)
                                    <div class="font-medium text-gray-700">{{ $med->editor->name ?? 'System' }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $med->updated_at->format('d/m/Y H:i') }}</div>
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="{{ route('medications.edit', $med->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('medications.destroy', $med->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Obat/Alkes ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                <p>Tidak ada data Obat & Alkes.</p>
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
