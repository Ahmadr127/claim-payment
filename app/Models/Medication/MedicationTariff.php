<?php

namespace App\Models\Medication;

use App\Models\Room\RoomClass;
use App\Support\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationTariff extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = ['id'];

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    public function roomClass(): BelongsTo
    {
        return $this->belongsTo(RoomClass::class);
    }
}
