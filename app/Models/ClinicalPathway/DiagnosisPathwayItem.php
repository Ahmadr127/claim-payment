<?php

namespace App\Models\ClinicalPathway;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DiagnosisPathwayItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function pathway(): BelongsTo
    {
        return $this->belongsTo(DiagnosisPathway::class, 'diagnosis_pathway_id');
    }

    public function item(): MorphTo
    {
        return $this->morphTo();
    }
}
