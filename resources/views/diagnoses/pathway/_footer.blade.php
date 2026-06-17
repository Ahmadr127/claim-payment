{{-- _footer.blade.php --}}
<div class="px-6 py-6 border-t border-gray-200 bg-white flex flex-col items-end">
    <div class="flex items-center justify-end mb-6">
        <span class="text-sm font-bold uppercase text-gray-700 mr-4">TOTAL KESELURUHAN</span>
        <span class="text-2xl font-bold text-green-700">
            Rp <span x-text="formatCurrency(getGrandTotal(activeTab))"></span>
        </span>
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
