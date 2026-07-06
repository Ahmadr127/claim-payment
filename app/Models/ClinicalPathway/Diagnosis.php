<?php

namespace App\Models\ClinicalPathway;

use App\Support\Traits\HasAuditLog;
use App\Support\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasFactory, HasAuditLog, HasAuditColumns;

    protected $guarded = ['id'];
}
