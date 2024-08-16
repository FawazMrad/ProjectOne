<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

class VenueFactory extends Factory
{
    protected $model = Venue::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company . ' Hall',
            'location' => $this->faker->address,
            'location_on_map' => $this->faker->url, // Simulated link to a map
            'max_capacity_no_chairs' => $this->faker->numberBetween(100, 1000),
            'max_capacity_chairs' => $this->faker->numberBetween(50, 500),
            'vip_chairs' => $this->faker->boolean(50) ? $this->faker->numberBetween(10, 50) : 0,
            'is_vip' => $this->faker->boolean(30),
            'website' => $this->faker->url,
            'rating' => $this->faker->randomFloat(1, 3, 5),
            'image' => $this->faker->imageUrl(640, 480, 'venues', true, 'venue'),
            'cost' => $this->faker->numberBetween(1000, 10000)
        ];
    }
}
