<?php

namespace App\Http\Controllers;

use App\Models\Medication\MedicationCommodity;
use Illuminate\Http\Request;

class MedicationCommodityController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');
        $commodities = MedicationCommodity::withCount('medications')
            ->with(['creator', 'editor'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10);

        return view('medication-commodities.index', compact('commodities', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:medication_commodities,name',
            'is_active' => 'boolean',
        ]);

        MedicationCommodity::create($validated);
        return redirect()->route('medication-commodities.index')->with('success', 'Komoditi Obat berhasil ditambahkan.');
    }

    public function update(Request $request, MedicationCommodity $medicationCommodity)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:medication_commodities,name,' . $medicationCommodity->id,
            'is_active' => 'boolean',
        ]);

        $medicationCommodity->update($validated);
        return redirect()->route('medication-commodities.index')->with('success', 'Komoditi Obat berhasil diperbarui.');
    }

    public function destroy(MedicationCommodity $medicationCommodity)
    {
        if ($medicationCommodity->medications()->exists()) {
            return redirect()->route('medication-commodities.index')->with('error', 'Komoditi tidak dapat dihapus karena masih digunakan oleh data obat/alkes.');
        }

        $medicationCommodity->delete();
        return redirect()->route('medication-commodities.index')->with('success', 'Komoditi Obat berhasil dihapus.');
    }
}
