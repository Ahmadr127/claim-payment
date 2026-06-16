<?php

namespace Database\Factories\Models\Room;

use App\Models\Room\RoomTariffType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomTariffTypeFactory extends Factory
{
    protected $model = RoomTariffType::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->word(),
            'name' => $this->faker->word(),
        ];
    }
}
