<!-- Modal Form (Create/Edit) -->
<div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 backdrop-blur-sm" x-cloak>
    <div class="relative p-4 w-full max-w-lg h-full md:h-auto"
         x-show="modalOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        
        <div class="relative bg-white rounded-xl shadow-xl overflow-hidden">
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900" x-text="isEdit ? 'Edit Kategori Layanan' : 'Tambah Kategori Layanan'"></h3>
                <button type="button" @click="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                    <i class="fas fa-times w-5 text-center"></i>
                </button>
            </div>
            
            <form :action="formAction" method="POST" class="p-6">
                @csrf
                <template x-if="isEdit">
                    @method('PUT')
                </template>
                
                <div class="space-y-5">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Kode Kategori <span class="text-red-500">*</span></label>
                        <input type="text" name="code" x-model="formData.code" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 uppercase">
                        <p class="mt-1 text-xs text-gray-500">Contoh: RADIOLOGI, LAB, KONSULTASI.</p>
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Nama Kategori <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="formData.name" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Deskripsi Singkat</label>
                        <input type="text" name="description" x-model="formData.description" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Urutan Tampil</label>
                            <input type="number" name="display_order" x-model="formData.display_order" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                            <select name="is_active" x-model="formData.is_active" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-200">
                    <button type="button" @click="closeModal()" class="text-gray-700 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 transition-colors">Batal</button>
                    <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors" x-text="isEdit ? 'Simpan Perubahan' : 'Tambah Kategori'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
