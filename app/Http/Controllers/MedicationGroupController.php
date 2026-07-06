<?php

namespace App\Http\Controllers;

use App\Models\Medication\MedicationGroup;
use Illuminate\Http\Request;

class MedicationGroupController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');
        $groups = MedicationGroup::withCount('medications')
            ->with(['creator', 'editor'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('code')
            ->orderBy('name')
            ->paginate(10);

        return view('medication-groups.index', compact('groups', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:medication_groups,code',
            'name' => 'required|string|max:150',
            'is_active' => 'boolean',
        ]);

        MedicationGroup::create($validated);
        return redirect()->route('medication-groups.index')->with('success', 'Golongan Obat berhasil ditambahkan.');
    }

    public function update(Request $request, MedicationGroup $medicationGroup)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:medication_groups,code,' . $medicationGroup->id,
            'name' => 'required|string|max:150',
            'is_active' => 'boolean',
        ]);

        $medicationGroup->update($validated);
        return redirect()->route('medication-groups.index')->with('success', 'Golongan Obat berhasil diperbarui.');
    }

    public function destroy(MedicationGroup $medicationGroup)
    {
        if ($medicationGroup->medications()->exists()) {
            return redirect()->route('medication-groups.index')->with('error', 'Golongan tidak dapat dihapus karena masih digunakan oleh data obat/alkes.');
        }

        $medicationGroup->delete();
        return redirect()->route('medication-groups.index')->with('success', 'Golongan Obat berhasil dihapus.');
    }
}
