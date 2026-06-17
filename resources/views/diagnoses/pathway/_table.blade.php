{{-- _table.blade.php --}}
<div class="overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-center font-semibold text-gray-600 w-16">NO</th>
                <th class="px-6 py-4 text-left font-semibold text-gray-600">ITEM LAYANAN</th>
                <th class="px-6 py-4 text-left font-semibold text-gray-600">DESKRIPSI</th>
                <th class="px-6 py-4 text-center font-semibold text-gray-600 w-24">QTY</th>
                <th class="px-6 py-4 text-center font-semibold text-gray-600 w-32">KODE</th>
                <th class="px-6 py-4 text-right font-semibold text-gray-600 w-48">HARGA SATUAN (Rp)</th>
                <th class="px-6 py-4 text-right font-semibold text-gray-600 w-48">TOTAL BIAYA (Rp)</th>
                <th class="px-6 py-4 text-center font-semibold text-gray-600 w-24">AKSI</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">

            {{-- Alpine-rendered rows for dynamically added services (Appears at TOP) --}}
            <template x-for="index in addedMatrixIndices" :key="'new_' + index">
                <tr class="hover:bg-green-50/30 transition-colors bg-green-50/10 border-l-2 border-l-green-400"
                    x-show="matchesSearch(index)">
                    <td class="px-6 py-4 text-center">
                        <i class="fas fa-star text-amber-400 text-xs" title="Layanan Tambahan"></i>
                    </td>
                    <td class="px-6 py-4 text-gray-800 font-medium" x-text="matrix[index].name"></td>
                    <td class="px-6 py-4 text-gray-500 text-sm" x-text="matrix[index].description || '-'"></td>
                    <td class="px-6 py-4 text-center">
                        <input type="number"
                               x-model.number="matrix[index].qty"
                               @input="updateTotal(index)"
                               min="0"
                               class="w-16 text-center border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-sm bg-white">
                    </td>
                    <td class="px-6 py-4 text-center text-gray-500" x-text="matrix[index].code"></td>
                    <td class="px-6 py-4 text-right">
                        @foreach($roomClasses as $rc)
                        <div x-show="activeTab === {{ $rc->id }}" x-cloak>
                            <input type="text"
                                   x-model="matrix[index].tariffs[{{ $rc->id }}].amount_formatted"
                                   @focus="onFocusAmount(index, {{ $rc->id }})"
                                   @blur="onBlurAmount(index, {{ $rc->id }})"
                                   @input="onInputAmount(index, {{ $rc->id }})"
                                   class="w-full text-right border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-sm text-gray-700 bg-white">
                        </div>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-gray-900">
                        @foreach($roomClasses as $rc)
                        <div x-show="activeTab === {{ $rc->id }}" x-cloak>
                            <span x-text="formatCurrency(matrix[index].tariffs[{{ $rc->id }}].total)"></span>
                        </div>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button type="button"
                                @click="removeService(index)"
                                class="text-red-400 hover:text-red-600 hover:bg-red-50 p-1.5 rounded-md transition-colors"
                                title="Hapus Layanan">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            </template>

            {{-- Blade-rendered rows from pathway matrix --}}
            @php $currentType = ''; @endphp

            @foreach($matrix as $index => $row)
                @if($currentType !== $row['type'])
                    @php $currentType = $row['type']; @endphp
                    <tr x-show="hasAnyMatchInType('{{ $currentType }}')" class="bg-slate-50/70 border-y border-gray-200">
                        <td></td>
                        <td colspan="7" class="px-6 py-3 font-bold uppercase text-teal-800 text-xs tracking-wider">
                            @if($currentType === 'RoomTariffType') TARIF KAMAR &amp; PERAWATAN
                            @elseif($currentType === 'MedicalService') JASA MEDIS &amp; TINDAKAN
                            @elseif($currentType === 'Medication') ALKES DAN OBAT RUANG RAWAT (CONSUMABLE)
                            @else {{ $currentType }} @endif
                        </td>
                    </tr>
                @endif

                <tr x-show="matchesSearch({{ $index }})" class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-center text-gray-500 font-medium">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 text-gray-800 font-medium">{{ $row['name'] }}</td>
                    <td class="px-6 py-4 text-gray-500 text-sm">{{ $row['description'] ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        <input type="number"
                               x-model.number="matrix[{{ $index }}].qty"
                               @input="updateTotal({{ $index }})"
                               min="0"
                               class="w-16 text-center border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-sm">
                    </td>
                    <td class="px-6 py-4 text-center text-gray-500">{{ $row['code'] }}</td>

                    {{-- Harga Satuan --}}
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

                    {{-- Aksi --}}
                    <td class="px-6 py-4 text-center">
                        <button type="button"
                                @click="removeService({{ $index }})"
                                class="text-red-400 hover:text-red-600 hover:bg-red-50 p-1.5 rounded-md transition-colors"
                                title="Hapus Layanan">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
</div>
