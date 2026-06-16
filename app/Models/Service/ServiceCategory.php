<?php

namespace App\Models\Service;

use App\Support\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = ['id'];

    public function medicalServices(): HasMany
    {
        return $this->hasMany(MedicalService::class);
    }
}
