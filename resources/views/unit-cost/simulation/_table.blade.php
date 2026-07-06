{{-- _table.blade.php --}}
<div class="overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-gray-200">
            <tr>
                <th class="px-3 py-3 text-center font-semibold text-gray-600 w-10">NO</th>
                <th class="px-3 py-3 text-center font-semibold text-gray-600 w-20">KODE</th>
                <th class="px-3 py-3 text-left font-semibold text-gray-600 min-w-40">ITEM</th>
                <th class="px-3 py-3 text-center font-semibold text-gray-600 w-14">QTY</th>
                <th class="px-3 py-3 text-center font-semibold text-gray-600 w-20">TIPE</th>
                <th class="px-3 py-3 text-right font-semibold text-gray-600 w-26">HNA (Rp)</th>
                <th class="px-3 py-3 text-center font-semibold text-gray-600 w-18">PPN (%)</th>
                <th class="px-3 py-3 text-right font-semibold text-gray-600 w-28">HNA+PPN (Rp)</th>
                <th class="px-3 py-3 text-center font-semibold text-gray-600 w-20">% SVC</th>
                <th class="px-3 py-3 text-right font-semibold text-gray-600 w-28">TOTAL (Rp)</th>
                <th class="px-3 py-3 text-center font-semibold text-gray-600 w-12">AKSI</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            {{-- Category: RoomTariffType --}}
            <tr class="bg-slate-50/70 border-y border-gray-200" x-show="hasAnyMatchInType('RoomTariffType')">
                <td></td>
                <td colspan="10" class="px-6 py-3 font-bold uppercase text-teal-800 text-xs tracking-wider">
                    TARIF KAMAR &amp; PERAWATAN
                </td>
            </tr>
            <template x-for="(row, index) in matrix" :key="index">
                <tr class="hover:bg-slate-50 transition-colors" x-show="row.type === 'RoomTariffType' && matchesSearch(index)">
                    <td class="px-3 py-2 text-center text-gray-500 font-medium text-xs" x-text="index + 1"></td>
                    <td class="px-3 py-2 text-center text-gray-500 font-mono text-xs" x-text="row.code"></td>
                    <td class="px-3 py-2 text-gray-800 font-medium text-xs">
                        <span x-text="row.name"></span>
                        <template x-if="row.is_new">
                            <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800">Baru</span>
                        </template>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <input type="number"
                               x-model.number="row.qty"
                               @input="updateTotal(index)"
                               min="0"
                               class="w-12 text-center border border-gray-300 rounded px-1 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-xs">
                    </td>
                    <td class="px-3 py-2 text-center">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">Kamar</span>
                    </td>
                    <td class="px-3 py-2 text-right">
                        @foreach($roomClasses as $rc)
                        <div x-show="activeTab === {{ $rc->id }}" x-cloak>
                            <input type="text"
                                   x-model="row.tariffs[{{ $rc->id }}].hna_formatted"
                                   @focus="onFocusHna(index, {{ $rc->id }})"
                                   @blur="onBlurHna(index, {{ $rc->id }})"
                                   @input="onInputHna(index, {{ $rc->id }})"
                                   placeholder="0"
                                   class="w-full text-right border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-xs bg-white font-medium">
                        </div>
                        @endforeach
                    </td>
                    <td class="px-3 py-2 text-center text-gray-300 text-xs">-</td>
                    <td class="px-3 py-2 text-center text-gray-300 text-xs">-</td>
                    <td class="px-3 py-2 text-center text-gray-300 text-xs">-</td>
                    <td class="px-3 py-2 text-right font-bold text-gray-900">
                        @foreach($roomClasses as $rc)
                        <div x-show="activeTab === {{ $rc->id }}" x-cloak>
                            <span x-text="'Rp ' + formatCurrency(row.tariffs[{{ $rc->id }}].total)" class="text-xs"></span>
                        </div>
                        @endforeach
                    </td>
                    <td class="px-3 py-2 text-center">
                        <button type="button" @click="removeService(index)" class="text-red-500 hover:text-red-700 transition-colors">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </td>
                </tr>
            </template>

            {{-- Category: MedicalService --}}
            <tr class="bg-slate-50/70 border-y border-gray-200" x-show="hasAnyMatchInType('MedicalService')">
                <td></td>
                <td colspan="10" class="px-6 py-3 font-bold uppercase text-teal-800 text-xs tracking-wider">
                    JASA MEDIS &amp; TINDAKAN
                </td>
            </tr>
            <template x-for="(row, index) in matrix" :key="index">
                <tr class="hover:bg-slate-50 transition-colors" x-show="row.type === 'MedicalService' && matchesSearch(index)">
                    <td class="px-3 py-2 text-center text-gray-500 font-medium text-xs" x-text="index + 1"></td>
                    <td class="px-3 py-2 text-center text-gray-500 font-mono text-xs" x-text="row.code"></td>
                    <td class="px-3 py-2 text-gray-800 font-medium text-xs">
                        <span x-text="row.name"></span>
                        <template x-if="row.is_new">
                            <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800">Baru</span>
                        </template>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <input type="number"
                               x-model.number="row.qty"
                               @input="updateTotal(index)"
                               min="0"
                               class="w-12 text-center border border-gray-300 rounded px-1 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-xs">
                    </td>
                    <td class="px-3 py-2 text-center">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700">Layanan</span>
                    </td>
                    <td class="px-3 py-2 text-right">
                        @foreach($roomClasses as $rc)
                        <div x-show="activeTab === {{ $rc->id }}" x-cloak>
                            <input type="text"
                                   x-model="row.tariffs[{{ $rc->id }}].hna_formatted"
                                   @focus="onFocusHna(index, {{ $rc->id }})"
                                   @blur="onBlurHna(index, {{ $rc->id }})"
                                   @input="onInputHna(index, {{ $rc->id }})"
                                   placeholder="0"
                                   class="w-full text-right border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-xs bg-white font-medium">
                        </div>
                        @endforeach
                    </td>
                    <td class="px-3 py-2 text-center text-gray-300 text-xs">-</td>
                    <td class="px-3 py-2 text-center text-gray-300 text-xs">-</td>
                    <td class="px-3 py-2 text-center">
                        @foreach($roomClasses as $rc)
                        <div x-show="activeTab === {{ $rc->id }}" x-cloak>
                            <input type="number"
                                   x-model.number="row.tariffs[{{ $rc->id }}].percentage"
                                   @input="updatePercentage(index, {{ $rc->id }})"
                                   step="0.01"
                                   placeholder="70"
                                   class="w-full text-center border border-gray-300 rounded px-1 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-xs">
                        </div>
                        @endforeach
                    </td>
                    <td class="px-3 py-2 text-right font-bold text-gray-900">
                        @foreach($roomClasses as $rc)
                        <div x-show="activeTab === {{ $rc->id }}" x-cloak>
                            <span x-text="'Rp ' + formatCurrency(row.tariffs[{{ $rc->id }}].total)" class="text-xs"></span>
                        </div>
                        @endforeach
                    </td>
                    <td class="px-3 py-2 text-center">
                        <button type="button" @click="removeService(index)" class="text-red-500 hover:text-red-700 transition-colors">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </td>
                </tr>
            </template>

            {{-- Category: Medication --}}
            <tr class="bg-slate-50/70 border-y border-gray-200" x-show="hasAnyMatchInType('Medication')">
                <td></td>
                <td colspan="10" class="px-6 py-3 font-bold uppercase text-teal-800 text-xs tracking-wider">
                    ALKES DAN OBAT
                </td>
            </tr>
            <template x-for="(row, index) in matrix" :key="index">
                <tr class="hover:bg-slate-50 transition-colors" x-show="row.type === 'Medication' && matchesSearch(index)">
                    <td class="px-3 py-2 text-center text-gray-500 font-medium text-xs" x-text="index + 1"></td>
                    <td class="px-3 py-2 text-center text-gray-500 font-mono text-xs" x-text="row.code"></td>
                    <td class="px-3 py-2 text-gray-800 font-medium text-xs">
                        <span x-text="row.name"></span>
                        <template x-if="row.is_new">
                            <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800">Baru</span>
                        </template>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <input type="number"
                               x-model.number="row.qty"
                               @input="updateTotal(index)"
                               min="0"
                               class="w-12 text-center border border-gray-300 rounded px-1 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-xs">
                    </td>
                    <td class="px-3 py-2 text-center">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">Obat/Alkes</span>
                    </td>
                    <td class="px-3 py-2 text-right">
                        @foreach($roomClasses as $rc)
                        <div x-show="activeTab === {{ $rc->id }}" x-cloak>
                            <input type="text"
                                   x-model="row.tariffs[{{ $rc->id }}].hna_formatted"
                                   @focus="onFocusHna(index, {{ $rc->id }})"
                                   @blur="onBlurHna(index, {{ $rc->id }})"
                                   @input="onInputHna(index, {{ $rc->id }})"
                                   placeholder="0"
                                   class="w-full text-right border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-xs bg-white font-medium">
                        </div>
                        @endforeach
                    </td>
                    <td class="px-3 py-2 text-center">
                        @foreach($roomClasses as $rc)
                        <div x-show="activeTab === {{ $rc->id }}" x-cloak>
                            <input type="number"
                                   x-model.number="row.tariffs[{{ $rc->id }}].ppn"
                                   @input="updateHnaPpn(index, {{ $rc->id }})"
                                   step="0.01"
                                   placeholder="11"
                                   class="w-full text-center border border-gray-300 rounded px-1 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-xs">
                        </div>
                        @endforeach
                    </td>
                    <td class="px-3 py-2 text-right">
                        @foreach($roomClasses as $rc)
                        <div x-show="activeTab === {{ $rc->id }}" x-cloak>
                            <span x-text="formatCurrency(row.tariffs[{{ $rc->id }}].hna_ppn)" class="font-medium text-gray-700 text-xs"></span>
                        </div>
                        @endforeach
                    </td>
                    <td class="px-3 py-2 text-center text-gray-300 text-xs">-</td>
                    <td class="px-3 py-2 text-right font-bold text-gray-900">
                        @foreach($roomClasses as $rc)
                        <div x-show="activeTab === {{ $rc->id }}" x-cloak>
                            <span x-text="'Rp ' + formatCurrency(row.tariffs[{{ $rc->id }}].total)" class="text-xs"></span>
                        </div>
                        @endforeach
                    </td>
                    <td class="px-3 py-2 text-center">
                        <button type="button" @click="removeService(index)" class="text-red-500 hover:text-red-700 transition-colors">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
</div>
