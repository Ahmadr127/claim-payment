<?php

namespace App\Http\Controllers;

use App\Models\Room\RoomClass;
use App\Models\Service\MedicalService;
use App\Models\Service\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalServiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');
        $categoryId = $request->input('category_id');

        $services = MedicalService::with('serviceCategory')
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('service_category_id', $categoryId);
            })
            ->orderBy('name')
            ->paginate(10);

        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();

        return view('services.index', compact('services', 'search', 'categoryId', 'categories'));
    }

    public function create()
    {
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        $roomClasses = RoomClass::where('is_active', true)->orderBy('display_order')->get();
        
        return view('services.create', compact('categories', 'roomClasses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'code' => 'required|string|max:100|unique:medical_services,code',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'is_active' => 'boolean',
            'tariffs' => 'array',
            'tariffs.*' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $service = MedicalService::create([
                'service_category_id' => $validated['service_category_id'],
                'code' => $validated['code'],
                'name' => $validated['name'],
                'unit' => $validated['unit'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            if (!empty($validated['tariffs'])) {
                $tariffsToInsert = [];
                foreach ($validated['tariffs'] as $roomClassId => $amount) {
                    if ($amount !== null && $amount !== '') {
                        $tariffsToInsert[] = [
                            'medical_service_id' => $service->id,
                            'room_class_id' => $roomClassId,
                            'amount' => $amount,
                            'effective_date' => date('Y-01-01'),
                            'is_active' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                
                if (count($tariffsToInsert) > 0) {
                    DB::table('service_tariffs')->insert($tariffsToInsert);
                }
            }

            DB::commit();
            return redirect()->route('services.index')->with('success', 'Layanan medis dan tarif berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit(MedicalService $service)
    {
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        $roomClasses = RoomClass::where('is_active', true)->orderBy('display_order')->get();
        
        $currentTariffs = DB::table('service_tariffs')
            ->where('medical_service_id', $service->id)
            ->where('is_active', true)
            ->get()
            ->keyBy('room_class_id');

        return view('services.edit', compact('service', 'categories', 'roomClasses', 'currentTariffs'));
    }

    public function update(Request $request, MedicalService $service)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'code' => 'required|string|max:100|unique:medical_services,code,' . $service->id,
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'is_active' => 'boolean',
            'tariffs' => 'array',
            'tariffs.*' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $service->update([
                'service_category_id' => $validated['service_category_id'],
                'code' => $validated['code'],
                'name' => $validated['name'],
                'unit' => $validated['unit'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Update tariffs
            if (isset($validated['tariffs'])) {
                foreach ($validated['tariffs'] as $roomClassId => $amount) {
                    if ($amount !== null && $amount !== '') {
                        DB::table('service_tariffs')->updateOrInsert(
                            [
                                'medical_service_id' => $service->id,
                                'room_class_id' => $roomClassId,
                                'effective_date' => date('Y-01-01') // using year start for simplicity
                            ],
                            [
                                'amount' => $amount,
                                'is_active' => true,
                                'updated_at' => now(),
                                'created_at' => DB::raw('COALESCE(created_at, NOW())')
                            ]
                        );
                    }
                }
            }

            DB::commit();
            return redirect()->route('services.index')->with('success', 'Layanan medis berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage());
        }
    }

    public function destroy(MedicalService $service)
    {
        try {
            // Check if it has any associated pathway items or claims (simulate check)
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
