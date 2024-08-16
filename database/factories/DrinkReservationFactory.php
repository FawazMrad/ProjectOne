<?php

namespace Database\Factories;

use App\Models\Drink;
use App\Models\Event;
use App\Models\DrinkReservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class DrinkReservationFactory extends Factory
{
    protected $model = DrinkReservation::class;

    public function definition()
    {
        $drink = Drink::inRandomOrder()->first();

        return [
            'drink_id' => $drink->id,
            'quantity' => $this->faker->numberBetween(1, 1000),
            'total_price' => $this->faker->numberBetween(10, $drink->cost * 10000),
        ];
    }
}
