<?php

namespace App\Models\Room;

use App\Support\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = ['id'];

    protected $casts = [
        'is_occupied' => 'boolean',
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    public function roomClass(): BelongsTo
    {
        return $this->belongsTo(RoomClass::class);
    }
}
