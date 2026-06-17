@extends('layouts.app')

@section('title', 'Tambah Layanan Medis')

@section('content')
<div class="w-full max-w-5xl mx-auto pb-10">
    <!-- Header -->
    <div class="mb-6">
        <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <span class="text-gray-500">Master Data</span>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                        <a href="{{ route('services.index') }}" class="hover:text-green-600 transition-colors">Layanan Medis</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                        <span class="text-gray-800 font-medium">Tambah Baru</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Layanan Medis</h1>
        <p class="text-sm text-gray-500 mt-1">Buat layanan medis baru beserta penetapan tarif per kelas kamarnya</p>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-start">
            <i class="fas fa-exclamation-circle mt-0.5 mr-3"></i>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('services.store') }}" method="POST">
        @csrf
        
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg mb-6 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Informasi Dasar Layanan</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Kode Layanan <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 uppercase placeholder-gray-400" placeholder="Contoh: LAB-001">
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Nama Layanan / Tindakan <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder-gray-400" placeholder="Contoh: Konsultasi Dokter Spesialis">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Kategori <span class="text-red-500">*</span></label>
                        <select name="service_category_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('service_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Satuan <span class="text-red-500">*</span></label>
                            <input type="text" name="unit" value="{{ old('unit', 'kali') }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                            <select name="is_active" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm border border-gray-200 rounded-lg mb-6 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Tarif Berdasarkan Kelas Kamar</h2>
                <span class="text-xs text-gray-500 font-normal bg-white px-2 py-1 rounded border border-gray-200">Kosongkan jika tidak ada tarif di kelas tersebut</span>
            </div>
            
            <div class="p-0 overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 w-1/3">Kelas Kamar</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Tarif (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-gray-50/30">
                        @foreach($roomClasses as $rc)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $rc->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $rc->code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="relative w-full max-w-xs">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="tariffs[{{ $rc->id }}]" value="{{ old('tariffs.'.$rc->id) }}" min="0" 
                                               class="pl-10 block w-full rounded-lg border-gray-300 bg-white border text-gray-900 focus:ring-green-500 focus:border-green-500 sm:text-sm p-2.5" 
                                               placeholder="0">
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 mb-10">
            <a href="{{ route('services.index') }}" class="text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 font-medium rounded-lg text-sm px-6 py-2.5 transition-colors">
                Batal
            </a>
            <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-6 py-2.5 transition-colors shadow-sm">
                <i class="fas fa-save mr-2"></i> Simpan Layanan
            </button>
        </div>
    </form>
</div>
@endsection
