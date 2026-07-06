<?php

namespace App\Models\UnitCost;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClinicalPathway\Diagnosis;
use App\Models\OrganizationUnit;
use App\Models\User;

class UnitCostAssignment extends Model
{
    protected $table = 'unit_cost_assignments';
    
    protected $fillable = [
        'diagnosis_id',
        'organization_unit_id',
        'is_active',
        'notes',
        'assigned_by',
        'assigned_at',
        'is_customized',
        'customized_data',
        'customized_at',
        'customized_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'assigned_at' => 'datetime',
        'is_customized' => 'boolean',
        'customized_data' => 'array',
        'customized_at' => 'datetime',
    ];

    /**
     * Get the diagnosis associated with this assignment
     */
    public function diagnosis()
    {
        return $this->belongsTo(Diagnosis::class);
    }

    /**
     * Get the organization unit associated with this assignment
     */
    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    /**
     * Get the user who assigned this
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the user who customized this
     */
    public function customizedBy()
    {
        return $this->belongsTo(User::class, 'customized_by');
    }

    /**
     * Scope to active assignments only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for a specific organization unit
     */
    public function scopeForUnit($query, $unitId)
    {
        return $query->where('organization_unit_id', $unitId);
    }
}
