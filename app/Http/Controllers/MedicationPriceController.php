<?php

namespace App\Http\Controllers;

use App\Models\Room\RoomClass;
use App\Models\Medication\Medication;
use App\Models\Medication\MedicationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicationPriceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');
        $categoryId = $request->input('category_id');

        $medications = Medication::with(['medicationCategory', 'group', 'creator', 'editor'])
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('item_code', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('medication_category_id', $categoryId);
            })
            ->orderBy('name')
            ->paginate(10);

        $categories = MedicationCategory::where('is_active', true)->orderBy('name')->get();

        return view('medication-prices.index', compact('medications', 'search', 'categoryId', 'categories'));
    }

    public function edit(Medication $medication)
    {
        $roomClasses = RoomClass::where('is_active', true)->orderBy('display_order')->get();
        
        $currentTariffs = DB::table('medication_tariffs')
            ->where('medication_id', $medication->id)
            ->where('is_active', true)
            ->get()
            ->keyBy('room_class_id');

        return view('medication-prices.edit', compact('medication', 'roomClasses', 'currentTariffs'));
    }

    public function update(Request $request, Medication $medication)
    {
        $validated = $request->validate([
            'hna' => 'nullable|numeric|min:0',
            'hna_ppn' => 'nullable|numeric|min:0',
            'ppn_rajal' => 'nullable|numeric|min:0|max:100',
            'ppn_ranap' => 'nullable|numeric|min:0|max:100',
            'tariffs' => 'array',
            'tariffs.*' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update HNA & PPN in medications table
            $medication->update([
                'hna' => $validated['hna'] ?? null,
                'hna_ppn' => $validated['hna_ppn'] ?? null,
                'ppn_rajal' => $validated['ppn_rajal'] ?? 0.00,
                'ppn_ranap' => $validated['ppn_ranap'] ?? 0.00,
            ]);

            // 2. Update Selling Tariffs in medication_tariffs table
            if (isset($validated['tariffs'])) {
                foreach ($validated['tariffs'] as $roomClassId => $amount) {
                    if ($amount !== null && $amount !== '') {
                        DB::table('medication_tariffs')->updateOrInsert(
                            [
                                'medication_id' => $medication->id,
                                'room_class_id' => $roomClassId,
                                'effective_date' => date('Y-01-01')
                            ],
                            [
                                'amount' => $amount,
                                'is_active' => true,
                                'updated_at' => now(),
                                'created_at' => DB::raw('COALESCE(created_at, NOW())')
                            ]
                        );
                    } else {
                        // If empty/null is sent, we can mark it inactive or delete it
                        DB::table('medication_tariffs')
                            ->where('medication_id', $medication->id)
                            ->where('room_class_id', $roomClassId)
                            ->update(['is_active' => false, 'updated_at' => now()]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('medication-prices.index')->with('success', 'Harga dan Tarif obat berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage());
        }
    }
}
