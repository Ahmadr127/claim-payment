@extends('layouts.app')

@section('title', 'Kelola Tarif Unit Cost Obat')

@section('content')
<div class="w-full max-w-5xl mx-auto pb-10" x-data="unitCostMedicationPricesForm()">
    <!-- Header -->
    <div class="mb-6">
        <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <span class="text-gray-500">Unit Cost</span>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                        <a href="{{ route('unit-cost-medication-prices.index') }}" class="hover:text-green-600 transition-colors">Tarif & Harga Obat (UC)</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                        <span class="text-gray-800 font-medium">Kelola Tarif UC</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">Kelola Tarif Unit Cost Obat: {{ $medication->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">Atur harga HNA dan persentase PPN global untuk perhitungan unit cost obat / alkes</p>
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

    <form action="{{ route('unit-cost-medication-prices.update', $medication->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Spesifikasi & Konfigurasi Harga Global (HNA & PPN) -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg mb-6 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Spesifikasi Obat & Konfigurasi Unit Cost Global</h2>
            </div>
            <div class="p-6 bg-slate-50/20 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-sm items-end">
                <div>
                    <span class="block text-gray-500 font-medium">Kode Item / Satuan</span>
                    <span class="font-bold text-gray-900 block mt-0.5">{{ $medication->item_code }} ({{ $medication->unit }})</span>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">HNA Obat (Rp):</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="hna" x-model.number="hna" @input="calculate()" min="0" step="any"
                               class="pl-10 block w-full rounded-lg border-gray-300 bg-white border text-gray-900 focus:ring-green-500 focus:border-green-500 sm:text-sm p-2.5 font-bold">
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">PPN (%):</label>
                    <input type="number" name="ppn_percentage" x-model.number="ppn" @input="calculate()" min="0" max="100" step="0.01"
                           class="block w-full rounded-lg border-gray-300 bg-white border text-gray-900 focus:ring-green-500 focus:border-green-500 sm:text-sm p-2.5 font-bold text-center">
                </div>
                <div>
                    <span class="block text-gray-500 font-medium">Hasil Akhir Unit Cost (HNA + PPN):</span>
                    <span class="font-bold text-teal-600 block mt-0.5 text-lg" x-text="formattedAmount"></span>
                </div>
            </div>
        </div>

        <!-- Simulasi Tarif Unit Cost per Kelas Kamar -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg mb-6 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Tarif Unit Cost per Kelas Kamar</h2>
                <p class="text-xs text-gray-500 mt-0.5">Berikut adalah simulasi tarif unit cost (HNA + PPN) di masing-masing kelas kamar berdasarkan nilai HNA dan PPN di atas</p>
            </div>
            
            <div class="p-0 overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 w-1/2">Kelas Kamar</th>
                            <th class="px-6 py-3 text-right font-semibold text-teal-800 w-1/2">Tarif Unit Cost Hasil Kalkulasi (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($roomClasses as $rc)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $rc->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $rc->code }}</div>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-teal-600 text-base" x-text="formattedAmount">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 mb-10">
            <a href="{{ route('unit-cost-medication-prices.index') }}" class="text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 font-medium rounded-lg text-sm px-6 py-2.5 transition-colors">
                Batal
            </a>
            <button type="submit" class="text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:ring-teal-300 font-medium rounded-lg text-sm px-6 py-2.5 transition-colors shadow-sm">
                <i class="fas fa-save mr-2"></i> Simpan Tarif
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('unitCostMedicationPricesForm', () => ({
        hna: {{ $medication->hna ?? 0.00 }},
        ppn: {{ $medication->ppn_percentage ?? 11.00 }},
        amount: 0,
        formattedAmount: 'Rp 0',

        init() {
            this.calculate();
        },

        calculate() {
            let activeHna = parseFloat(this.hna) || 0;
            let activePpn = parseFloat(this.ppn) || 0;

            if (activeHna > 0) {
                this.amount = Math.round(activeHna * (1 + (activePpn / 100)));
            } else {
                this.amount = 0;
            }

            this.formattedAmount = 'Rp ' + new Intl.NumberFormat('id-ID').format(this.amount);
        }
    }));
});
</script>
@endpush
