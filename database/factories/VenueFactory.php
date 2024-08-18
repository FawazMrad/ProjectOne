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
            'max_capacity_no_chairs' => $this->faker->numberBetween(200, 300),
            'max_capacity_chairs' => $this->faker->numberBetween(100, 150),
            'vip_chairs' => $this->faker->numberBetween(50, 100),
            'is_vip' => $this->faker->boolean(30),
            'website' => $this->faker->url,
            'rating' => $this->faker->randomFloat(1, 3, 5),
            'image' => 'https://prestigiousvenues.com/wp-content/uploads/bb-plugin/cache/Gala-Dinner-Venue-In-London-Gibson-Hall-Prestigious-Venues-panorama-e59dc799b93c25c0dc960e904af705e0-59099a98687f6.jpg',
            'cost' => $this->faker->numberBetween(100, 1000)
        ];
    }
}
