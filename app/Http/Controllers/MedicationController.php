<?php

namespace App\Http\Controllers;

use App\Models\Medication\Medication;
use App\Models\Medication\MedicationCategory;
use App\Models\Medication\MedicationGroup;
use App\Models\Medication\MedicationCommodity;
use App\Models\Medication\MedicationProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');
        $categoryId = $request->input('category_id');

        $medications = Medication::with(['medicationCategory', 'group', 'commodity', 'productGroup', 'creator', 'editor'])
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('item_code', 'like', "%{$search}%")
                      ->orWhere('active_ingredient', 'like', "%{$search}%")
                      ->orWhereHas('group', function($g) use ($search) {
                          $g->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                      });
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('medication_category_id', $categoryId);
            })
            ->orderBy('name')
            ->paginate(10);

        $categories = MedicationCategory::where('is_active', true)->orderBy('name')->get();

        return view('medications.index', compact('medications', 'search', 'categoryId', 'categories'));
    }

    public function create()
    {
        $categories = MedicationCategory::where('is_active', true)->orderBy('name')->get();
        $groups = MedicationGroup::where('is_active', true)->orderBy('code')->orderBy('name')->get();
        $commodities = MedicationCommodity::where('is_active', true)->orderBy('name')->get();
        $productGroups = MedicationProductGroup::where('is_active', true)->orderBy('name')->get();
        
        return view('medications.create', compact('categories', 'groups', 'commodities', 'productGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medication_category_id' => 'required|exists:medication_categories,id',
            'item_code' => 'required|string|max:100|unique:medications,item_code',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'is_active' => 'boolean',
            'medication_group_id' => 'nullable|exists:medication_groups,id',
            'medication_commodity_id' => 'nullable|exists:medication_commodities,id',
            'medication_product_group_id' => 'nullable|exists:medication_product_groups,id',
            'indication' => 'nullable|string',
            'active_ingredient' => 'nullable|string',
            'detailed_composition' => 'nullable|string',
            'hna' => 'nullable|numeric|min:0',
            'ppn_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $hna = $validated['hna'] ?? 0;
            $ppn = $validated['ppn_percentage'] ?? 11;
            $hnaPpn = (int) round($hna * (1 + ($ppn / 100)));

            Medication::create([
                'medication_category_id' => $validated['medication_category_id'],
                'item_code' => $validated['item_code'],
                'name' => $validated['name'],
                'unit' => $validated['unit'],
                'is_active' => $validated['is_active'] ?? true,
                'medication_group_id' => $validated['medication_group_id'] ?? null,
                'medication_commodity_id' => $validated['medication_commodity_id'] ?? null,
                'medication_product_group_id' => $validated['medication_product_group_id'] ?? null,
                'indication' => $validated['indication'] ?? null,
                'active_ingredient' => $validated['active_ingredient'] ?? null,
                'detailed_composition' => $validated['detailed_composition'] ?? null,
                'hna' => $hna,
                'ppn_percentage' => $ppn,
                'hna_ppn' => $hnaPpn,
            ]);

            return redirect()->route('medications.index')->with('success', 'Obat & Alkes berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit(Medication $medication)
    {
        $categories = MedicationCategory::where('is_active', true)->orderBy('name')->get();
        $groups = MedicationGroup::where('is_active', true)->orderBy('code')->orderBy('name')->get();
        $commodities = MedicationCommodity::where('is_active', true)->orderBy('name')->get();
        $productGroups = MedicationProductGroup::where('is_active', true)->orderBy('name')->get();
        
        return view('medications.edit', compact('medication', 'categories', 'groups', 'commodities', 'productGroups'));
    }

    public function update(Request $request, Medication $medication)
    {
        $validated = $request->validate([
            'medication_category_id' => 'required|exists:medication_categories,id',
            'item_code' => 'required|string|max:100|unique:medications,item_code,' . $medication->id,
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'is_active' => 'boolean',
            'medication_group_id' => 'nullable|exists:medication_groups,id',
            'medication_commodity_id' => 'nullable|exists:medication_commodities,id',
            'medication_product_group_id' => 'nullable|exists:medication_product_groups,id',
            'indication' => 'nullable|string',
            'active_ingredient' => 'nullable|string',
            'detailed_composition' => 'nullable|string',
            'hna' => 'nullable|numeric|min:0',
            'ppn_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $hna = $validated['hna'] ?? 0;
            $ppn = $validated['ppn_percentage'] ?? 11;
            $hnaPpn = (int) round($hna * (1 + ($ppn / 100)));

            $medication->update([
                'medication_category_id' => $validated['medication_category_id'],
                'item_code' => $validated['item_code'],
                'name' => $validated['name'],
                'unit' => $validated['unit'],
                'is_active' => $validated['is_active'] ?? true,
                'medication_group_id' => $validated['medication_group_id'] ?? null,
                'medication_commodity_id' => $validated['medication_commodity_id'] ?? null,
                'medication_product_group_id' => $validated['medication_product_group_id'] ?? null,
                'indication' => $validated['indication'] ?? null,
                'active_ingredient' => $validated['active_ingredient'] ?? null,
                'detailed_composition' => $validated['detailed_composition'] ?? null,
                'hna' => $hna,
                'ppn_percentage' => $ppn,
                'hna_ppn' => $hnaPpn,
            ]);

            return redirect()->route('medications.index')->with('success', 'Obat & Alkes berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage());
        }
    }

    public function destroy(Medication $medication)
    {
        try {
            $hasPathwayItems = DB::table('diagnosis_pathway_items')
                ->where('item_type', Medication::class)
                ->where('item_id', $medication->id)
                ->exists();

            if ($hasPathwayItems) {
                return back()->with('error', 'Obat/Alkes tidak dapat dihapus karena sedang digunakan dalam Clinical Pathway.');
            }

            DB::beginTransaction();
            DB::table('medication_tariffs')->where('medication_id', $medication->id)->delete();
            $medication->delete();
            DB::commit();
            
            return redirect()->route('medications.index')->with('success', 'Obat & Alkes berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus Obat/Alkes: ' . $e->getMessage());
        }
    }
}
