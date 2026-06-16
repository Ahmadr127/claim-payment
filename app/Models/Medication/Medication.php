<?php

namespace App\Models\Medication;

use App\Support\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = ['id'];

    public function medicationCategory(): BelongsTo
    {
        return $this->belongsTo(MedicationCategory::class);
    }

    public function tariffs(): HasMany
    {
        return $this->hasMany(MedicationTariff::class);
    }
}
