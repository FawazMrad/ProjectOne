<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
            'birth_date' => $this->faker->date('Y-m-d', '2004-12-31'),
            'points' => $this->faker->numberBetween(0, 10000),
            'rating' => $this->faker->randomFloat(1, 0, 5),
            'followers' => $this->faker->numberBetween(0, 10000),
            'following' => $this->faker->numberBetween(0, 10000),
            'profile_pic' => $this->faker->imageUrl(640, 480, 'people', true, 'profile'),
            'qr_code' => $this->faker->imageUrl(300, 300, 'qr', true, 'QR code'),
           // 'created_at' => $this->faker->dateTimeBetween(now(),now()->addWeek()), // Random date between 1 year ago and 1 week from now
            'created_at' => $this->faker->dateTimeBetween(now()->subYear(3), now()), // Random date between 1 year ago and 1 week from now
        ];
    }
}
