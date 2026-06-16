<?php

namespace App\Models\Billing;

use App\Models\Patient\Hospitalization;
use App\Models\Service\ServiceTariff;
use App\Support\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceCharge extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = ['id'];

    public function hospitalization(): BelongsTo
    {
        return $this->belongsTo(Hospitalization::class);
    }

    public function serviceTariff(): BelongsTo
    {
        return $this->belongsTo(ServiceTariff::class);
    }
}
