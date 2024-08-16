<?php

namespace Database\Factories;

use App\Models\Attendee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AttendeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attendee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'status' => $this->faker->randomElement(['INVITED', 'ATTENDING', 'PURCHASED', 'CANCELLED']),
//            'checked_in' => $this->faker->boolean(),
            'ticket_price' => $this->faker->randomFloat(2, 10, 500), // Random price between 10 and 500
            'ticket_type' => $this->faker->randomElement(['REGULAR', 'VIP']),
            'seat_number' => $this->faker->bothify('??-###'), // Example seat number format: AB-123
            'discount' => $this->faker->randomFloat(2, 0, 100), // Random discount between 0 and 100
            'qr_code' => Str::random(40), // Generates a random 40-character string
        ];
    }
}
