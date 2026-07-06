<?php

namespace App\Http\Controllers;

use App\Models\Service\MedicalService;
use App\Models\Service\ServiceGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalServiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');
        $groupId = $request->input('group_id');

        $services = MedicalService::with(['serviceGroup', 'creator', 'editor'])
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($groupId, function ($query, $groupId) {
                return $query->where('service_group_id', $groupId);
            })
            ->orderBy('name')
            ->paginate(10);

        $groups = ServiceGroup::where('is_active', true)->orderBy('name')->get();

        return view('services.index', compact('services', 'search', 'groupId', 'groups'));
    }

    public function create()
    {
        $groups = ServiceGroup::where('is_active', true)->orderBy('name')->get();
        return view('services.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_group_id' => 'required|exists:service_groups,id',
            'code' => 'required|string|max:100|unique:medical_services,code',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        try {
            MedicalService::create([
                'service_group_id' => $validated['service_group_id'],
                'code' => $validated['code'],
                'name' => $validated['name'],
                'unit' => $validated['unit'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()->route('services.index')->with('success', 'Layanan medis berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit(MedicalService $service)
    {
        $groups = ServiceGroup::where('is_active', true)->orderBy('name')->get();
        return view('services.edit', compact('service', 'groups'));
    }

    public function update(Request $request, MedicalService $service)
    {
        $validated = $request->validate([
            'service_group_id' => 'required|exists:service_groups,id',
            'code' => 'required|string|max:100|unique:medical_services,code,' . $service->id,
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        try {
            $service->update([
                'service_group_id' => $validated['service_group_id'],
                'code' => $validated['code'],
                'name' => $validated['name'],
                'unit' => $validated['unit'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()->route('services.index')->with('success', 'Layanan medis berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage());
        }
    }

    public function destroy(MedicalService $service)
    {
        try {
            $hasPathwayItems = DB::table('diagnosis_pathway_items')
                ->where('item_type', MedicalService::class)
                ->where('item_id', $service->id)
                ->exists();

            if ($hasPathwayItems) {
                return back()->with('error', 'Layanan tidak dapat dihapus karena sedang digunakan dalam Clinical Pathway.');
            }

            DB::beginTransaction();
            DB::table('service_tariffs')->where('medical_service_id', $service->id)->delete();
            $service->delete();
            DB::commit();
            
            return redirect()->route('services.index')->with('success', 'Layanan medis berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus layanan: ' . $e->getMessage());
        }
    }
}
