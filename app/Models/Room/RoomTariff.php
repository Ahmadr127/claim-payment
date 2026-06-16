<?php

namespace App\Models\Room;

use App\Support\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomTariff extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = ['id'];

    public function roomClass(): BelongsTo
    {
        return $this->belongsTo(RoomClass::class);
    }

    public function roomTariffType(): BelongsTo
    {
        return $this->belongsTo(RoomTariffType::class);
    }
}
