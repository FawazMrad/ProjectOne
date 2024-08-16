<?php

namespace Database\Factories;

use App\Models\Furniture;
use Illuminate\Database\Eloquent\Factories\Factory;

class FurnitureFactory extends Factory
{
    protected $model = Furniture::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // List of possible names and types
        $names = ['Sofa', 'Dining Table', 'Chair', 'Bed', 'Coffee Table', 'Wardrobe', 'Bookshelf'];
        $otherTypes = ['seating', 'table', 'storage', 'sleeping'];

        // Randomly select a name
        $name = $this->faker->randomElement($names);

        return [
            'name' => $name,
            'type' => $name === 'Chair'
                ? $this->faker->randomElement(['_vipChair', '_regularChair']) // Restricted values for chairs
                : $this->faker->randomElement($otherTypes), // Other types for other furniture
            'quantity' => $this->faker->numberBetween(1, 100),
            'image' => $this->faker->imageUrl(640, 480, 'furniture', true, 'furniture'), // Generic furniture image URL
            'cost' => $this->faker->numberBetween(50, 2000),
        ];
    }
}
