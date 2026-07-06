<?php

namespace App\Models\Service;

use App\Support\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceGroup extends Model
{
    use HasFactory, HasAuditColumns;

    protected $table = 'service_groups';

    protected $guarded = ['id'];

    public function medicalServices(): HasMany
    {
        return $this->hasMany(MedicalService::class, 'service_group_id');
    }
}
