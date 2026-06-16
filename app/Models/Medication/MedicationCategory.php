<?php

namespace App\Models\Medication;

use App\Support\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicationCategory extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = ['id'];

    public function medications(): HasMany
    {
        return $this->hasMany(Medication::class);
    }
}
