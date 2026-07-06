<?php

namespace App\Http\Controllers;

use App\Models\ClinicalPathway\Diagnosis;
use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    public function index(Request $request)
    {
        $query = Diagnosis::with(['creator', 'editor']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', '%' . $search . '%')
                  ->orWhere('icd_code', 'ilike', '%' . $search . '%');
            });
        }

        $diagnoses = $query->orderBy('id', 'asc')->paginate(15)->withQueryString();

        return view('diagnoses.index', compact('diagnoses'));
    }
}
