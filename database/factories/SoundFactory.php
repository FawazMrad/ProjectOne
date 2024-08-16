<?php

namespace Database\Factories;

use App\Models\Sound;
use Illuminate\Database\Eloquent\Factories\Factory;

class SoundFactory extends Factory
{
    protected $model = Sound::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['LIVE', 'RECORDED', 'DJ']), // Enum values
            'genre' => $this->faker->word(), // Random genre
            'artist' => $this->faker->name(), // Random artist name, optional
            'rating' => $this->faker->randomFloat(1, 0, 5), // Random float rating between 0.0 and 5.0
            'image' => $this->faker->imageUrl(640, 480, 'music', true, 'sound'), // Placeholder image URL
            'cost' => $this->faker->randomFloat(2, 10, 500), // Random cost between 10.00 and 500.00
        ];
    }
}
