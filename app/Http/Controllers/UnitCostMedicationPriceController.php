<?php

namespace App\Http\Controllers;

use App\Models\Room\RoomClass;
use App\Models\Medication\Medication;
use App\Models\Medication\MedicationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitCostMedicationPriceController extends Controller
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

        return view('unit-cost.medication-prices.index', compact('medications', 'search', 'categoryId', 'categories'));
    }

    public function edit(Medication $medication)
    {
        $roomClasses = RoomClass::where('is_active', true)->orderBy('display_order')->get();
        
        $generalTariffs = DB::table('medication_tariffs')
            ->where('medication_id', $medication->id)
            ->where('is_active', true)
            ->get()
            ->keyBy('room_class_id');

        return view('unit-cost.medication-prices.edit', compact('medication', 'roomClasses', 'generalTariffs'));
    }

    public function update(Request $request, Medication $medication)
    {
        $validated = $request->validate([
            'hna' => 'nullable|numeric|min:0',
            'ppn_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $hna = $validated['hna'] !== null && $validated['hna'] !== '' ? (float) $validated['hna'] : 0;
            $ppn = $validated['ppn_percentage'] !== null && $validated['ppn_percentage'] !== '' ? (float) $validated['ppn_percentage'] : 11.00;
            $amount = (int) round($hna * (1 + ($ppn / 100)));

            $medication->update([
                'hna' => $hna,
                'ppn_percentage' => $ppn,
                'hna_ppn' => $amount,
            ]);

            DB::commit();
            return redirect()->route('unit-cost-medication-prices.index')->with('success', 'Harga obat (unit cost) berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage());
        }
    }
}
