<?php

namespace App\Http\Controllers;

use App\Models\Medication\MedicationProductGroup;
use Illuminate\Http\Request;

class MedicationProductGroupController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');
        $groups = MedicationProductGroup::withCount('medications')
            ->with(['creator', 'editor'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10);

        return view('medication-product-groups.index', compact('groups', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:medication_product_groups,name',
            'is_active' => 'boolean',
        ]);

        MedicationProductGroup::create($validated);
        return redirect()->route('medication-product-groups.index')->with('success', 'Kelompok Barang berhasil ditambahkan.');
    }

    public function update(Request $request, MedicationProductGroup $medicationProductGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:medication_product_groups,name,' . $medicationProductGroup->id,
            'is_active' => 'boolean',
        ]);

        $medicationProductGroup->update($validated);
        return redirect()->route('medication-product-groups.index')->with('success', 'Kelompok Barang berhasil diperbarui.');
    }

    public function destroy(MedicationProductGroup $medicationProductGroup)
    {
        if ($medicationProductGroup->medications()->exists()) {
            return redirect()->route('medication-product-groups.index')->with('error', 'Kelompok Barang tidak dapat dihapus karena masih digunakan oleh data obat/alkes.');
        }

        $medicationProductGroup->delete();
        return redirect()->route('medication-product-groups.index')->with('success', 'Kelompok Barang berhasil dihapus.');
    }
}
