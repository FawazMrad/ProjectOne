<?php

namespace Database\Factories;

use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class StationFactory extends Factory
{
    protected $model = Station::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'governorate' => $this->faker->randomElement([
                'DAMASCUS', 'ALEPPO', 'IDLIB', 'HAMAH', 'LATTAKIA',
                'TARTOUS', 'HOMS', 'SWAIDA', 'DARAA', 'QUANYTIRA',
                'DAYRALZWR', 'ALHASAKAH', 'ALRAQQAH', 'RIFDIMASHQ'
            ]), // Random governorate
            'name' => $this->faker->company() . ' ' . $this->faker->unique()->randomNumber(4), // Ensure unique station name by appending a random number
            'password' => Hash::make('password'), // Hashed password
            'location' => $this->faker->address(), // Random address
            'manager_name' => $this->faker->name(), // Random manager name
            'manager_email' => $this->faker->unique()->safeEmail(), // Unique manager email
            'manager_id_picture' => $this->faker->imageUrl(640, 480, 'people', true, 'manager'), // Placeholder ID picture
            'balance' => $this->faker->numberBetween(1000, 100000), // Random balance between 1,000 and 100,000
        ];
    }
}
