<?php

namespace App\Models\Billing;

use App\Models\Patient\Hospitalization;
use App\Models\Room\RoomTariff;
use App\Support\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomCharge extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = ['id'];

    public function hospitalization(): BelongsTo
    {
        return $this->belongsTo(Hospitalization::class);
    }

    public function roomTariff(): BelongsTo
    {
        return $this->belongsTo(RoomTariff::class);
    }
}
