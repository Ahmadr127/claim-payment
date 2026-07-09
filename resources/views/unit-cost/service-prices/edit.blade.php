@extends('layouts.app')

@section('title', 'Kelola Tarif Unit Cost Layanan')

@section('content')
<div class="w-full max-w-5xl mx-auto pb-10" x-data="unitCostServicePricesForm()">
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
                        <a href="{{ route('unit-cost-service-prices.index') }}" class="hover:text-green-600 transition-colors">Tarif Layanan Medis (UC)</a>
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
        <h1 class="text-2xl font-bold text-gray-900">Kelola Tarif Unit Cost: {{ $service->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">Sesuaikan tarif dasar tindakan atau layanan medis beserta persentase jasa pelayanan (% SVC)</p>
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

    <form action="{{ route('unit-cost-service-prices.update', $service->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Info Layanan & Persentase SVC -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg mb-6 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Spesifikasi Tindakan & Konfigurasi Persentase</h2>
            </div>
            <div class="p-6 bg-slate-50/20 grid grid-cols-1 md:grid-cols-4 gap-6 text-sm items-end">
                <div>
                    <span class="block text-gray-500 font-medium">Kode Layanan</span>
                    <span class="font-bold text-gray-900 block mt-0.5">{{ $service->code }}</span>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium">Nama Tindakan</span>
                    <span class="font-bold text-gray-900 block mt-0.5">{{ $service->name }}</span>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium">Golongan / Satuan</span>
                    <span class="font-bold text-gray-900 block mt-0.5">
                        {{ $service->serviceGroup->name ?? '-' }} ({{ $service->unit }})
                    </span>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Persentase SVC (%):</label>
                    <input type="number" name="percentage" x-model.number="percentage" @input="updateAllTotals()" min="0" max="100" step="0.01"
                           class="block w-full rounded-lg border-gray-300 bg-white border text-gray-900 focus:ring-green-500 focus:border-green-500 sm:text-sm p-2.5 font-bold text-center">
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm border border-gray-200 rounded-lg mb-6 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Tarif Berdasarkan Kelas Kamar</h2>
                <span class="text-xs text-gray-500 font-normal bg-white px-2 py-1 rounded border border-gray-200">Perubahan tarif di bawah ini akan memperbarui Tarif Umum asalnya</span>
            </div>
            
            <div class="p-0 overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 w-1/3">Kelas Kamar</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 w-1/3">Tarif Dasar / HNA (Rp)</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600 w-1/3">Tarif Unit Cost Akhir (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($roomClasses as $rc)
                            @php
                                $tariff = $currentTariffs->get($rc->id);
                                $amount = $tariff ? $tariff->amount : '';
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors" x-data="serviceRow({{ $rc->id }}, '{{ $amount }}')">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $rc->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $rc->code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="relative w-full max-w-xs">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="tariffs[{{ $rc->id }}]" x-model.number="amount" @input="calculate()" min="0" 
                                               class="pl-10 block w-full rounded-lg border-gray-300 bg-white border text-gray-900 focus:ring-green-500 focus:border-green-500 sm:text-sm p-2.5 font-medium" 
                                               placeholder="0">
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900 text-base">
                                    <span x-text="formattedTotal"></span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 mb-10">
            <a href="{{ route('unit-cost-service-prices.index') }}" class="text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 font-medium rounded-lg text-sm px-6 py-2.5 transition-colors">
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
    Alpine.data('unitCostServicePricesForm', () => ({
        percentage: {{ $service->percentage ?? 70.00 }},
        
        init() {
            // Listen for percentage change to update all rows
            this.$watch('percentage', () => {
                this.updateAllTotals();
            });
        },

        updateAllTotals() {
            window.dispatchEvent(new CustomEvent('percentage-changed', {
                detail: { percentage: this.percentage }
            }));
        }
    }));

    Alpine.data('serviceRow', (rcId, initialAmount) => ({
        amount: initialAmount !== '' ? parseFloat(initialAmount) : null,
        percentage: 70,
        total: 0,
        formattedTotal: 'Rp 0',

        init() {
            this.percentage = Alpine.$data(document.querySelector('[x-data="unitCostServicePricesForm()"]')).percentage;
            this.calculate();
            
            window.addEventListener('percentage-changed', (e) => {
                this.percentage = e.detail.percentage;
                this.calculate();
            });
        },

        calculate() {
            let activeAmount = parseFloat(this.amount) || 0;
            let activePct = parseFloat(this.percentage) || 0;

            if (activeAmount > 0) {
                this.total = Math.round(activeAmount * (activePct / 100));
            } else {
                this.total = 0;
            }

            this.formattedTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(this.total);
        }
    }));
});
</script>
@endpush
