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
            'ticket_price' => $this->faker->randomFloat(2, 10, 500),
            'ticket_type' => $this->faker->randomElement(['REGULAR', 'VIP']),
            'seat_number' => $this->faker->regexify('[vr]{1}[0-9]{3}'),
            'discount' => $this->faker->randomFloat(2, 0, 100),
            'qr_code' => Str::random(40),
        ];
    }
}
