{{-- _footer.blade.php --}}
<div class="px-6 py-6 border-t border-gray-200 bg-white flex flex-col items-end">
    <div class="flex flex-col items-end w-full space-y-3 mb-6">
        <div class="flex items-center justify-between w-full max-w-sm">
            <span class="text-sm font-semibold text-gray-500">TOTAL BIAYA</span>
            <span class="text-lg font-bold text-gray-900">
                Rp <span x-text="formatCurrency(getGrandTotal(activeTab))"></span>
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
                Rp <span x-text="formatCurrency(Math.round(getGrandTotal(activeTab) * (adminFeePercentage / 100)))"></span>
            </span>
        </div>
        <div class="flex items-center justify-between w-full max-w-sm pt-3 border-t border-gray-200">
            <span class="text-sm font-bold uppercase text-gray-700">BIAYA SETELAH ADMIN</span>
            <span class="text-2xl font-bold text-green-700">
                Rp <span x-text="formatCurrency(Math.round(getGrandTotal(activeTab) * (1 + (adminFeePercentage / 100))))"></span>
            </span>
        </div>
    </div>

    <div class="flex gap-3 mt-4">
        <a href="{{ route('diagnoses.index') }}" wire:navigate
           class="px-6 py-2.5 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium text-sm transition-colors">
            Batalkan
        </a>
        <button type="button" @click="saveChanges()"
                class="px-6 py-2.5 bg-green-800 text-white rounded-md hover:bg-green-900 font-medium text-sm transition-colors shadow-sm">
            Simpan Perubahan
        </button>
    </div>
</div>
