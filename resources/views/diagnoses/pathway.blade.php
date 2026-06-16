@extends('layouts.app')

@section('title', 'Simulasi Tarif Umum')

@section('content')
<div class="w-full mx-auto pb-10" x-data="simulationForm()">
    
    {{-- Breadcrumb & Title --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2">
                    <li>
                        <a href="{{ route('diagnoses.index') }}" wire:navigate class="hover:text-green-800 transition-colors">Daftar Diagnosa & Tarif Umum</a>
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
    </div>

    {{-- Search & Tabs --}}
    <div class="flex flex-col lg:flex-row gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 lg:w-80 flex items-center px-4">
            <i class="fas fa-search text-gray-400"></i>
            <input type="text" x-model="searchQuery" placeholder="Cari nama/kode layanan..." class="w-full border-none focus:ring-0 text-sm ml-2 py-3 bg-transparent">
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-2 flex overflow-x-auto flex-1">
            @foreach($roomClasses as $rc)
            <button 
                @click="activeTab = {{ $rc->id }}"
                class="flex-1 py-3 px-6 text-sm font-bold uppercase tracking-wider rounded-md transition-colors whitespace-nowrap"
                :class="activeTab === {{ $rc->id }} ? 'bg-green-800 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
            >
                {{ $rc->code }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Tab Contents --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-center font-semibold text-gray-600 w-16">NO</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-600">ITEM LAYANAN</th>
                        <th class="px-6 py-4 text-center font-semibold text-gray-600 w-24">QTY</th>
                        <th class="px-6 py-4 text-center font-semibold text-gray-600 w-32">KODE</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-600 w-48">HARGA SATUAN (Rp)</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-600 w-48">TOTAL BIAYA (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="(row, index) in matrix" :key="index">
                        <template x-if="true">
                            {{-- We must wrap multiple rows in a single template but x-for requires one root, so we use fragments or just output tr. Since alpine needs one root per template item, we'll use <tbody x-for> if possible, or just generate the categories on the backend.
                                 Since categories change, it's easier to build a grouped matrix structure. Let's group in the JS. --}}
                        </template>
                    </template>
                    
                    {{-- Instead of fully Alpine loops which makes grouping hard, let's use Blade to render the structure and Alpine for the inputs --}}
                    @php
                        $currentType = '';
                    @endphp
                    
                    @foreach($matrix as $index => $row)
                        @if($currentType !== $row['type'])
                            @php $currentType = $row['type']; @endphp
                            <tr x-show="hasAnyMatchInType('{{ $currentType }}')" class="bg-slate-50/70 border-y border-gray-200">
                                <td></td>
                                <td colspan="5" class="px-6 py-3 font-bold uppercase text-teal-800 text-xs tracking-wider">
                                    @if($currentType === 'RoomTariffType') TARIF KAMAR & PERAWATAN
                                    @elseif($currentType === 'MedicalService') JASA MEDIS & TINDAKAN
                                    @elseif($currentType === 'Medication') ALKES DAN OBAT RUANG RAWAT (CONSUMABLE)
                                    @else {{ $currentType }} @endif
                                </td>
                            </tr>
                        @endif
                        <tr x-show="matchesSearch({{ $index }})" class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-center text-gray-500 font-medium">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-gray-800 font-medium">{{ $row['name'] }}</td>
                            <td class="px-6 py-4 text-center">
                                <input type="number" 
                                       x-model.number="matrix[{{ $index }}].qty"
                                       @input="updateTotal({{ $index }})"
                                       class="w-16 text-center border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-sm">
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500">{{ $row['code'] }}</td>
                            
                            {{-- Harga Satuan (shows different input based on activeTab) --}}
                            <td class="px-6 py-4 text-right">
                                @foreach($roomClasses as $rc)
                                <div x-show="activeTab === {{ $rc->id }}" x-cloak>
                                    <input type="text" 
                                           x-model="matrix[{{ $index }}].tariffs[{{ $rc->id }}].amount_formatted"
                                           @focus="onFocusAmount({{ $index }}, {{ $rc->id }})"
                                           @blur="onBlurAmount({{ $index }}, {{ $rc->id }})"
                                           @input="onInputAmount({{ $index }}, {{ $rc->id }})"
                                           class="w-full text-right border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-sm text-gray-700">
                                </div>
                                @endforeach
                            </td>
                            
                            {{-- Total Biaya --}}
                            <td class="px-6 py-4 text-right font-bold text-gray-900">
                                @foreach($roomClasses as $rc)
                                <div x-show="activeTab === {{ $rc->id }}" x-cloak>
                                    <span x-text="formatCurrency(matrix[{{ $index }}].tariffs[{{ $rc->id }}].total)"></span>
                                </div>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Footer --}}
        <div class="px-6 py-6 border-t border-gray-200 bg-white flex flex-col items-end">
            <div class="flex items-center justify-end mb-6">
                <span class="text-sm font-bold uppercase text-gray-700 mr-4">TOTAL KESELURUHAN</span>
                <span class="text-2xl font-bold text-green-700">
                    Rp <span x-text="formatCurrency(getGrandTotal(activeTab))"></span>
                </span>
            </div>
            
            <div class="flex gap-3 mt-4">
                <a href="{{ route('diagnoses.index') }}" wire:navigate class="px-6 py-2.5 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium text-sm transition-colors">
                    Batalkan
                </a>
                <button type="button" @click="saveChanges()" class="px-6 py-2.5 bg-green-800 text-white rounded-md hover:bg-green-900 font-medium text-sm transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('simulationForm', () => ({
        activeTab: {{ $roomClasses->first()->id ?? 0 }},
        searchQuery: '',
        
        // Matrix needs to be initialized with formatted amounts for inputs
        matrix: {!! json_encode(collect($matrix)->map(function($row) use ($roomClasses) {
            foreach ($roomClasses as $rc) {
                // Add amount_formatted for the UI input
                $row['tariffs'][$rc->id]['amount_formatted'] = $row['tariffs'][$rc->id]['amount'] ? number_format($row['tariffs'][$rc->id]['amount'], 0, ',', '.') : '0';
            }
            return $row;
        })) !!},
        
        formatCurrency(value) {
            if (value === null || value === undefined || isNaN(value)) return '0';
            return new Intl.NumberFormat('id-ID').format(value);
        },
        
        parseCurrency(value) {
            if (!value) return 0;
            return parseInt(value.toString().replace(/[^0-9]/g, ''), 10) || 0;
        },
        
        onFocusAmount(index, rcId) {
            let val = this.matrix[index].tariffs[rcId].amount_formatted;
            if (val === '0') {
                this.matrix[index].tariffs[rcId].amount_formatted = '';
            }
            // We no longer strip formatting on focus, so the user sees dots while editing
        },
        onBlurAmount(index, rcId) {
            let val = this.matrix[index].tariffs[rcId].amount_formatted;
            if (val === '') {
                this.matrix[index].tariffs[rcId].amount_formatted = '0';
                this.matrix[index].tariffs[rcId].amount = 0;
            }
            this.updateTotal(index);
        },
        onInputAmount(index, rcId) {
            let val = this.matrix[index].tariffs[rcId].amount_formatted;
            // remove all non-digits
            let cleanVal = val.toString().replace(/\D/g, '');
            
            if (cleanVal !== '') {
                let numericVal = parseInt(cleanVal, 10);
                this.matrix[index].tariffs[rcId].amount = numericVal;
                // format back with dots
                this.matrix[index].tariffs[rcId].amount_formatted = numericVal.toLocaleString('id-ID');
            } else {
                this.matrix[index].tariffs[rcId].amount = 0;
                this.matrix[index].tariffs[rcId].amount_formatted = '';
            }
            
            this.updateTotal(index);
        },
        
        updateTotal(index) {
            let qty = parseInt(this.matrix[index].qty) || 0;
            
            // Update total for all room classes just in case QTY changed
            Object.keys(this.matrix[index].tariffs).forEach(rcId => {
                let amount = this.matrix[index].tariffs[rcId].amount || 0;
                this.matrix[index].tariffs[rcId].total = qty * amount;
            });
        },
        
        getGrandTotal(rcId) {
            if(!rcId || this.matrix.length === 0) return 0;
            return this.matrix.reduce((sum, row) => sum + (row.tariffs[rcId].total || 0), 0);
        },
        
        matchesSearch(index) {
            if (!this.searchQuery) return true;
            const q = this.searchQuery.toLowerCase();
            const row = this.matrix[index];
            return (row.name && row.name.toLowerCase().includes(q)) || 
                   (row.code && row.code.toLowerCase().includes(q));
        },
        
        hasAnyMatchInType(type) {
            if (!this.searchQuery) return true;
            const q = this.searchQuery.toLowerCase();
            return this.matrix.some(row => 
                row.type === type && 
                ((row.name && row.name.toLowerCase().includes(q)) || 
                 (row.code && row.code.toLowerCase().includes(q)))
            );
        },
        
        getKamarQty() {
            let kamarRow = this.matrix.find(row => row.code === 'KAMAR' || (row.name && row.name.toLowerCase().includes('kamar rawat')));
            return kamarRow ? (parseInt(kamarRow.qty) || 0) : {{ $pathway->length_of_stay }};
        },
        
        saveChanges() {
            // Simulasi menyimpan data
            window.Toast && Toast.success('Perubahan simulasi berhasil disimpan (Simulasi)');
            // console.log(JSON.stringify(this.matrix));
        }
    }));
});
</script>
@endsection
