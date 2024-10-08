<?php

namespace Database\Factories;

use App\Models\Favourite;
use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavouriteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Favourite::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'event_id' => Event::inRandomOrder()->first()->id,  // Selects a random existing Event
            'priority_level' => $this->faker->randomElement(['LOW', 'MID', 'HIGH']),  // Randomly assigns a priority level
        ];
    }
}
