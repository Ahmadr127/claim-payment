<?php

namespace App\Models\Patient;

use App\Support\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = ['id'];
}
