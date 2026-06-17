<?php

namespace App\Http\Controllers;

use App\Models\Service\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');
        $categories = ServiceCategory::withCount('medicalServices')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(10);

        return view('service-categories.index', compact('categories', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:service_categories,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'integer|min:1',
            'is_active' => 'boolean',
        ]);

        ServiceCategory::create($validated);
        return redirect()->route('service-categories.index')->with('success', 'Kategori Layanan berhasil ditambahkan.');
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:service_categories,code,' . $serviceCategory->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'integer|min:1',
            'is_active' => 'boolean',
        ]);

        $serviceCategory->update($validated);
        return redirect()->route('service-categories.index')->with('success', 'Kategori Layanan berhasil diperbarui.');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        if ($serviceCategory->medicalServices()->exists()) {
            return redirect()->route('service-categories.index')->with('error', 'Kategori tidak dapat dihapus karena masih memiliki layanan medis yang terikat.');
        }
        
        $serviceCategory->delete();
        return redirect()->route('service-categories.index')->with('success', 'Kategori Layanan berhasil dihapus.');
    }
}
