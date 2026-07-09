@extends('layouts.app')

@section('title', 'Unit Cost Layanan Medis')

@section('content')
<div class="w-full mx-auto pb-10">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <span class="text-gray-500">Unit Cost</span>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-xs mx-2"></i>
                            <span class="text-gray-800 font-medium">Tarif Layanan Medis (UC)</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Tarif Layanan Medis (Unit Cost)</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data tarif unit cost dasar tindakan atau layanan medis per kelas kamar</p>
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
        <form action="{{ route('unit-cost-service-prices.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4">
            
            <div class="w-full md:w-1/3">
                <select name="group_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" onchange="this.form.submit()">
                    <option value="">-- Semua Golongan --</option>
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}" {{ $groupId == $g->id ? 'selected' : '' }}>
                            {{ $g->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="relative w-full md:w-96 flex">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari kode atau nama layanan..." 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm transition-colors">
            </div>
            
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg border border-gray-300 transition-colors">Cari</button>
            <a href="{{ route('unit-cost-service-prices.index') }}" class="bg-white hover:bg-gray-50 text-gray-500 px-4 py-2 rounded-lg border border-gray-300 transition-colors text-center">Reset</a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-b-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Kode Layanan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Layanan / Tindakan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Golongan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Satuan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($services as $index => $srv)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">{{ $services->firstItem() + $index }}</td>
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ $srv->code }}</td>
                            <td class="px-6 py-4 text-gray-900 font-semibold">{{ $srv->name }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $srv->serviceGroup->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center text-gray-500">{{ $srv->unit }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($srv->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('unit-cost-service-prices.edit', $srv->id) }}" class="text-teal-600 hover:text-teal-900 font-medium inline-flex items-center gap-1.5 transition-colors" title="Kelola Tarif Unit Cost">
                                    <i class="fas fa-calculator"></i> Kelola Tarif UC
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                <p>Tidak ada data layanan medis.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($services->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
