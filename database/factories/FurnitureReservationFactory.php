<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Furniture;
use App\Models\FurnitureReservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class FurnitureReservationFactory extends Factory
{
    protected $model = FurnitureReservation::class;

    public function definition()
    {
        $furniture = Furniture::inRandomOrder()->first();


        return [
            'furniture_id' => $furniture->id,
            'quantity' => $this->faker->numberBetween(1, $furniture->quantity),
            'cost' => $this->faker->numberBetween(10, $furniture->quantity * $furniture->cost),
        ];
    }
}
