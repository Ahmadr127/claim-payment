<?php

namespace App\Models\Medication;

use App\Support\Traits\HasAuditLog;
use App\Support\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicationGroup extends Model
{
    use HasFactory, HasAuditLog, HasAuditColumns;

    protected $guarded = ['id'];

    public function medications(): HasMany
    {
        return $this->hasMany(Medication::class, 'medication_group_id');
    }
}
