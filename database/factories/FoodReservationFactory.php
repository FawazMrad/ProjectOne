<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\Event;
use App\Models\FoodReservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodReservationFactory extends Factory
{
    protected $model = FoodReservation::class;

    public function definition()
    {
        $food = Food::inRandomOrder()->first();

        return [
            'food_id' => $food->id,
            'quantity' => $this->faker->numberBetween(1, 1000),
            'total_price' => $this->faker->numberBetween(10, $food->cost * 10000),
        ];
    }
}
