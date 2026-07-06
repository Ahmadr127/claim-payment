<?php

namespace App\Models\Room;

use App\Support\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomClass extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = ['id'];

    protected static function newFactory()
    {
        return \Database\Factories\Models\Room\RoomClassFactory::new();
    }

    public function tariffs(): HasMany
    {
        return $this->hasMany(RoomTariff::class);
    }
}
