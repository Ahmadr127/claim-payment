<?php

namespace App\Models\Room;

use App\Support\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomTariffType extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = ['id'];
}
