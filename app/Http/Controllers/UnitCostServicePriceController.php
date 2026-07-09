<?php

namespace App\Http\Controllers;

use App\Models\Room\RoomClass;
use App\Models\Service\MedicalService;
use App\Models\Service\ServiceGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitCostServicePriceController extends Controller
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

        return view('unit-cost.service-prices.index', compact('services', 'search', 'groupId', 'groups'));
    }

    public function edit(MedicalService $service)
    {
        $roomClasses = RoomClass::where('is_active', true)->orderBy('display_order')->get();
        
        $currentTariffs = DB::table('service_tariffs')
            ->where('medical_service_id', $service->id)
            ->where('is_active', true)
            ->get()
            ->keyBy('room_class_id');

        return view('unit-cost.service-prices.edit', compact('service', 'roomClasses', 'currentTariffs'));
    }

    public function update(Request $request, MedicalService $service)
    {
        $validated = $request->validate([
            'percentage' => 'nullable|numeric|min:0|max:100',
            'tariffs' => 'array',
            'tariffs.*' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update global service percentage
            $percentage = $validated['percentage'] !== null && $validated['percentage'] !== '' ? (float) $validated['percentage'] : 70.00;
            $service->update([
                'percentage' => $percentage
            ]);

            // Update service tariffs per class
            if (isset($validated['tariffs'])) {
                foreach ($validated['tariffs'] as $roomClassId => $amount) {
                    if ($amount !== null && $amount !== '') {
                        DB::table('service_tariffs')->updateOrInsert(
                            [
                                'medical_service_id' => $service->id,
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
                        DB::table('service_tariffs')
                            ->where('medical_service_id', $service->id)
                            ->where('room_class_id', $roomClassId)
                            ->update(['is_active' => false, 'updated_at' => now()]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('unit-cost-service-prices.index')->with('success', 'Tarif unit cost layanan medis berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage());
        }
    }
}
