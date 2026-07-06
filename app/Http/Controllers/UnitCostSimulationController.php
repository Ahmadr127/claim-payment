<?php

namespace App\Http\Controllers;

use App\Models\ClinicalPathway\Diagnosis;
use App\Models\OrganizationUnit;
use App\Models\Room\RoomClass;
use App\Models\UnitCost\UnitCostAssignment;
use App\Services\UnitCostCalculationService;
use Illuminate\Http\Request;

class UnitCostSimulationController extends Controller
{
    protected $calculationService;

    public function __construct(UnitCostCalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    /**
     * Show unit cost simulation for a specific diagnosis and organization unit
     */
    public function show(OrganizationUnit $organizationUnit, Diagnosis $diagnosis)
    {
        // Check if this diagnosis is assigned to this unit
        $assignment = UnitCostAssignment::where('diagnosis_id', $diagnosis->id)
            ->where('organization_unit_id', $organizationUnit->id)
            ->where('is_active', true)
            ->first();

        if (!$assignment) {
            return redirect()->back()->with('error', 'Diagnosa ini tidak ditugaskan ke unit Anda atau tidak aktif.');
        }

        // Get pathway
        $pathway = $diagnosis->pathway;
        if (!$pathway) {
            return redirect()->back()->with('error', 'Simulasi tarif untuk diagnosa ini belum tersedia.');
        }

        // Calculate matrix using HNA + PPN logic
        $matrix = $this->calculationService->calculateMatrix($diagnosis, $organizationUnit);
        
        // Load customized data if available
        if ($assignment->is_customized && $assignment->customized_data) {
            $customizedItems = $assignment->customized_data;
            
            // Apply customized values to matrix
            foreach ($customizedItems as $customizedItem) {
                $index = $customizedItem['index'];
                
                if (isset($matrix[$index])) {
                    if (!empty($customizedItem['deleted'])) {
                        $matrix[$index]['deleted'] = true;
                    }
                    $matrix[$index]['qty'] = $customizedItem['qty'] ?? $matrix[$index]['qty'];
                    
                    // Apply tariff customizations
                    foreach ($customizedItem['tariffs'] as $rcId => $tariffData) {
                        if (isset($matrix[$index]['tariffs'][$rcId])) {
                            $matrix[$index]['tariffs'][$rcId]['amount'] = $tariffData['amount'] ?? $matrix[$index]['tariffs'][$rcId]['amount'];
                            $matrix[$index]['tariffs'][$rcId]['base_amount'] = $tariffData['base_amount'] ?? $matrix[$index]['tariffs'][$rcId]['base_amount'] ?? $matrix[$index]['tariffs'][$rcId]['amount'];
                            $matrix[$index]['tariffs'][$rcId]['hna'] = $tariffData['hna'] ?? $matrix[$index]['tariffs'][$rcId]['hna'];
                            $matrix[$index]['tariffs'][$rcId]['ppn'] = $tariffData['ppn'] ?? $matrix[$index]['tariffs'][$rcId]['ppn'];
                            $matrix[$index]['tariffs'][$rcId]['hna_ppn'] = $tariffData['hna_ppn'] ?? $matrix[$index]['tariffs'][$rcId]['hna_ppn'];
                            $matrix[$index]['tariffs'][$rcId]['percentage'] = $tariffData['percentage'] ?? $matrix[$index]['tariffs'][$rcId]['percentage'];
                            $matrix[$index]['tariffs'][$rcId]['total'] = $tariffData['total'] ?? $matrix[$index]['tariffs'][$rcId]['total'];
                        }
                    }
                } elseif (!empty($customizedItem['is_new'])) {
                    // Reconstruct newly added item
                    $matrix[$index] = [
                        'id' => $customizedItem['id'],
                        'type' => $customizedItem['type'],
                        'name' => $customizedItem['name'],
                        'code' => $customizedItem['code'] ?? '-',
                        'qty' => $customizedItem['qty'] ?? 1,
                        'is_new' => true,
                        'deleted' => $customizedItem['deleted'] ?? false,
                        'tariffs' => []
                    ];
                    
                    foreach ($customizedItem['tariffs'] as $rcId => $tariffData) {
                        $matrix[$index]['tariffs'][$rcId] = [
                            'amount' => $tariffData['amount'] ?? 0,
                            'base_amount' => $tariffData['base_amount'] ?? 0,
                            'hna' => $tariffData['hna'] ?? 0,
                            'ppn' => $tariffData['ppn'] ?? 0,
                            'hna_ppn' => $tariffData['hna_ppn'] ?? 0,
                            'percentage' => $tariffData['percentage'] ?? 0,
                            'total' => $tariffData['total'] ?? 0,
                        ];
                    }
                }
            }
            // Sort matrix by index to maintain correct ordering if indices are out of order
            ksort($matrix);
            // Re-index array so it is a sequential list for JSON encoding
            $matrix = array_values($matrix);
        }
        
        $roomClasses = RoomClass::where('is_active', true)->orderBy('display_order')->get();

        return view('unit-cost.simulation.index', compact(
            'diagnosis',
            'organizationUnit',
            'assignment',
            'pathway',
            'matrix',
            'roomClasses'
        ));
    }

    /**
     * Show list of assigned diagnoses for current user's organization unit
     */
    public function index(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $organizationUnit = $user->organizationUnit;

        if (!$organizationUnit) {
            return redirect()->back()->with('error', 'Anda tidak memiliki unit organisasi yang terkait.');
        }

        $search = $request->input('q');

        $assignments = UnitCostAssignment::with(['diagnosis', 'assignedBy', 'customizedBy'])
            ->where('organization_unit_id', $organizationUnit->id)
            ->where('is_active', true)
            ->when($search, function ($query, $search) {
                return $query->whereHas('diagnosis', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('icd_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('assigned_at', 'desc')
            ->get();

        return view('unit-cost.index', compact('organizationUnit', 'assignments', 'search'));
    }

    /**
     * Export simulation as PDF (future enhancement)
     */
    public function exportPDF(OrganizationUnit $organizationUnit, Diagnosis $diagnosis)
    {
        // TODO: Implement PDF export
        return redirect()->back()->with('info', 'Fitur export PDF sedang dalam pengembangan.');
    }

    /**
     * Export simulation as Excel (future enhancement)
     */
    public function exportExcel(OrganizationUnit $organizationUnit, Diagnosis $diagnosis)
    {
        // TODO: Implement Excel export
        return redirect()->back()->with('info', 'Fitur export Excel sedang dalam pengembangan.');
    }

    /**
     * Save simulation draft/changes
     */
    public function saveDraft(Request $request, OrganizationUnit $organizationUnit, Diagnosis $diagnosis)
    {
        $assignment = UnitCostAssignment::where('diagnosis_id', $diagnosis->id)
            ->where('organization_unit_id', $organizationUnit->id)
            ->where('is_active', true)
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'Assignment tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.index' => 'required|integer',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.tariffs' => 'required|array',
        ]);

        // Update assignment with the customized data
        $user = \Illuminate\Support\Facades\Auth::user();
        
        $adminFeePercentage = $request->input('admin_fee_percentage');
        if ($adminFeePercentage !== null) {
            $diagnosis->update([
                'admin_fee_percentage' => (float) $adminFeePercentage
            ]);
        }

        $assignment->update([
            'customized_data' => $request->input('items'),
            'is_customized' => true,
            'customized_at' => now(),
            'customized_by' => $user ? $user->id : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Simulasi berhasil disimpan',
            'assignment' => $assignment
        ]);
    }
}
