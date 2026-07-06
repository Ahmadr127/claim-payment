{{-- _footer.blade.php --}}
<div class="border-t border-gray-200 bg-slate-50 px-6 py-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- Tarif Kamar --}}
        <div>
            <div class="text-xs font-semibold text-gray-500 uppercase mb-2 tracking-wider">Tarif Kamar</div>
            <div class="text-2xl font-bold text-blue-600">
                @foreach($roomClasses as $rc)
                <span x-show="activeTab === {{ $rc->id }}" x-cloak x-text="'Rp ' + formatCurrency(categoryTotals[{{ $rc->id }}]['RoomTariffType'])"></span>
                @endforeach
            </div>
        </div>

        {{-- Jasa Medis --}}
        <div>
            <div class="text-xs font-semibold text-gray-500 uppercase mb-2 tracking-wider">Jasa Medis</div>
            <div class="text-2xl font-bold text-purple-600">
                @foreach($roomClasses as $rc)
                <span x-show="activeTab === {{ $rc->id }}" x-cloak x-text="'Rp ' + formatCurrency(categoryTotals[{{ $rc->id }}]['MedicalService'])"></span>
                @endforeach
            </div>
        </div>

        {{-- Obat & Alkes --}}
        <div>
            <div class="text-xs font-semibold text-gray-500 uppercase mb-2 tracking-wider">Obat & Alkes</div>
            <div class="text-2xl font-bold text-green-600">
                @foreach($roomClasses as $rc)
                <span x-show="activeTab === {{ $rc->id }}" x-cloak x-text="'Rp ' + formatCurrency(categoryTotals[{{ $rc->id }}]['Medication'])"></span>
                @endforeach
            </div>
        </div>

        {{-- Grand Total --}}
        <div class="border-l border-gray-300 pl-6">
            <div class="text-xs font-semibold text-gray-500 uppercase mb-2 tracking-wider">Total Unit Cost</div>
            <div class="text-3xl font-bold text-green-700">
                @foreach($roomClasses as $rc)
                <span x-show="activeTab === {{ $rc->id }}" x-cloak x-text="'Rp ' + formatCurrency(categoryTotals[{{ $rc->id }}]['total'])"></span>
                @endforeach
            </div>
        </div>
    </div>
</div>
