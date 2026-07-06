@extends('layouts.app')

@section('title', 'Tambah Obat & Alkes')

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
                        <a href="{{ route('medications.index') }}" class="hover:text-green-600 transition-colors">Obat & Alkes</a>
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
        <h1 class="text-2xl font-bold text-gray-900">Tambah Obat & Alkes</h1>
        <p class="text-sm text-gray-500 mt-1">Buat data obat/alkes baru beserta penetapan tarif penjualannya per kelas kamar</p>
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

    <form action="{{ route('medications.store') }}" method="POST">
        @csrf
        
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg mb-6 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Informasi Dasar Obat & Alkes</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Kode Item <span class="text-red-500">*</span></label>
                        <input type="text" name="item_code" value="{{ old('item_code') }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 uppercase placeholder-gray-400" placeholder="Contoh: OBTO1259">
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Nama Obat / Alkes <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder-gray-400" placeholder="Contoh: TERFACEF INJ">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Kategori <span class="text-red-500">*</span></label>
                        <select name="medication_category_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('medication_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Satuan <span class="text-red-500">*</span></label>
                            <input type="text" name="unit" value="{{ old('unit', 'pcs') }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
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
            <div class="bg-slate-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Spesifikasi & Golongan Obat/Alkes</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Golongan Obat</label>
                        <select name="medication_group_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                            <option value="">-- Pilih Golongan --</option>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}" {{ old('medication_group_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->code ? '['.$g->code.'] ' : '' }}{{ $g->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Komoditi</label>
                        <select name="medication_commodity_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                            <option value="">-- Pilih Komoditi --</option>
                            @foreach($commodities as $c)
                                <option value="{{ $c->id }}" {{ old('medication_commodity_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Kelompok Barang</label>
                        <select name="medication_product_group_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                            <option value="">-- Pilih Kelompok Barang --</option>
                            @foreach($productGroups as $pg)
                                <option value="{{ $pg->id }}" {{ old('medication_product_group_id') == $pg->id ? 'selected' : '' }}>
                                    {{ $pg->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Zat Aktif</label>
                        <input type="text" name="active_ingredient" value="{{ old('active_ingredient') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Contoh: Ceftriaxone">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Komposisi Detail</label>
                        <textarea name="detailed_composition" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Contoh: Ceftriaxone 1g; air untuk injeksi ad 10ml">{{ old('detailed_composition') }}</textarea>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Indikasi</label>
                        <textarea name="indication" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Contoh: Meredakan gejala infeksi saluran napas berat">{{ old('indication') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 mb-10">
            <a href="{{ route('medications.index') }}" class="text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 font-medium rounded-lg text-sm px-6 py-2.5 transition-colors">
                Batal
            </a>
            <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-6 py-2.5 transition-colors shadow-sm">
                <i class="fas fa-save mr-2"></i> Simpan Obat/Alkes
            </button>
        </div>
    </form>
</div>
@endsection
