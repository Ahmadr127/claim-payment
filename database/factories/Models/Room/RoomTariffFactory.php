<?php

namespace Database\Factories\Models\Room;

use App\Models\Room\RoomClass;
use App\Models\Room\RoomTariff;
use App\Models\Room\RoomTariffType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomTariffFactory extends Factory
{
    protected $model = RoomTariff::class;

    public function definition(): array
    {
        return [
            'room_class_id' => RoomClass::factory(),
            'room_tariff_type_id' => RoomTariffType::factory(),
            'amount' => $this->faker->numberBetween(100000, 1000000),
            'effective_date' => now()->subMonth(),
            'is_active' => true,
        ];
    }
}
