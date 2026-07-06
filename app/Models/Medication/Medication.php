<?php

namespace App\Models\Medication;

use App\Support\Traits\HasAuditLog;
use App\Support\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    use HasFactory, HasAuditLog, HasAuditColumns;

    protected $guarded = ['id'];

    public function medicationCategory(): BelongsTo
    {
        return $this->belongsTo(MedicationCategory::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(MedicationGroup::class, 'medication_group_id');
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(MedicationCommodity::class, 'medication_commodity_id');
    }

    public function productGroup(): BelongsTo
    {
        return $this->belongsTo(MedicationProductGroup::class, 'medication_product_group_id');
    }

    public function tariffs(): HasMany
    {
        return $this->hasMany(MedicationTariff::class);
    }
}
