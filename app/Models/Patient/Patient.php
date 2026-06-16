<?php

namespace App\Models\Patient;

use App\Support\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = ['id'];
    
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function hospitalizations(): HasMany
    {
        return $this->hasMany(Hospitalization::class);
    }
}
