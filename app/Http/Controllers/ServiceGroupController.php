<?php

namespace App\Http\Controllers;

use App\Models\Service\ServiceGroup;
use Illuminate\Http\Request;

class ServiceGroupController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');
        $groups = ServiceGroup::withCount('medicalServices')
            ->with(['creator', 'editor'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(10);

        return view('service-groups.index', compact('groups', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:service_groups,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'integer|min:1',
            'is_active' => 'boolean',
        ]);

        ServiceGroup::create($validated);
        return redirect()->route('service-groups.index')->with('success', 'Golongan Layanan berhasil ditambahkan.');
    }

    public function update(Request $request, ServiceGroup $serviceGroup)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:service_groups,code,' . $serviceGroup->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'integer|min:1',
            'is_active' => 'boolean',
        ]);

        $serviceGroup->update($validated);
        return redirect()->route('service-groups.index')->with('success', 'Golongan Layanan berhasil diperbarui.');
    }

    public function destroy(ServiceGroup $serviceGroup)
    {
        if ($serviceGroup->medicalServices()->exists()) {
            return redirect()->route('service-groups.index')->with('error', 'Golongan tidak dapat dihapus karena masih memiliki layanan medis yang terikat.');
        }
        
        $serviceGroup->delete();
        return redirect()->route('service-groups.index')->with('success', 'Golongan Layanan berhasil dihapus.');
    }
}
