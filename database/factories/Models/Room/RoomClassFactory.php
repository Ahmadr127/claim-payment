<?php

namespace Database\Factories\Models\Room;

use App\Models\Room\RoomClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomClassFactory extends Factory
{
    protected $model = RoomClass::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->word(),
            'name' => $this->faker->word(),
            'is_active' => true,
        ];
    }
}
