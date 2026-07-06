{{-- _footer.blade.php --}}
<div class="px-6 py-6 border-t border-gray-200 bg-white flex flex-col items-end">
    <div class="flex flex-col items-end w-full space-y-3">
        <div class="flex items-center justify-between w-full max-w-sm">
            <span class="text-sm font-semibold text-gray-500">TOTAL BIAYA</span>
            <span class="text-lg font-bold text-gray-900">
                @foreach($roomClasses as $rc)
                <span x-show="activeTab === {{ $rc->id }}" x-cloak x-text="'Rp ' + formatCurrency(categoryTotals[{{ $rc->id }}]['total'])"></span>
                @endforeach
            </span>
        </div>
        <div class="flex items-center justify-between w-full max-w-sm">
            <div class="flex items-center text-sm font-semibold text-gray-500">
                ADMIN
                <div class="mx-2 relative w-16">
                    <input type="number" step="0.01" x-model="adminFeePercentage" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded focus:ring-green-500 focus:border-green-500 block w-full px-2 py-1 text-center" />
                </div>
                %
            </div>
            <span class="text-lg font-bold text-gray-900">
                @foreach($roomClasses as $rc)
                <span x-show="activeTab === {{ $rc->id }}" x-cloak x-text="'Rp ' + formatCurrency(Math.round(categoryTotals[{{ $rc->id }}]['total'] * (adminFeePercentage / 100)))"></span>
                @endforeach
            </span>
        </div>
        <div class="flex items-center justify-between w-full max-w-sm pt-3 border-t border-gray-200">
            <span class="text-sm font-bold uppercase text-gray-700">BIAYA SETELAH ADMIN</span>
            <span class="text-2xl font-bold text-green-700">
                @foreach($roomClasses as $rc)
                <span x-show="activeTab === {{ $rc->id }}" x-cloak x-text="'Rp ' + formatCurrency(Math.round(categoryTotals[{{ $rc->id }}]['total'] * (1 + (adminFeePercentage / 100))))"></span>
                @endforeach
            </span>
        </div>
    </div>
</div>
