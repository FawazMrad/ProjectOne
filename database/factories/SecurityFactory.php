<?php

namespace Database\Factories;

use App\Models\Security;
use Illuminate\Database\Eloquent\Factories\Factory;

class SecurityFactory extends Factory
{
    protected $model = Security::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'clothes_color' => $this->faker->safeColorName(), // Generates a random safe color name
            'quantity' => $this->faker->numberBetween(1, 50), // Random quantity between 1 and 50
            'cost' => $this->faker->numberBetween(20, 100), // Random cost between 100 and 5000
        ];
    }
}
