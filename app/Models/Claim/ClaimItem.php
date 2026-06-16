<?php

namespace App\Models\Claim;

use App\Support\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimItem extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = ['id'];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }
}
