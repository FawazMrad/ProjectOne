<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Helpers\QR_CodeHelper;

class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? null,
            'category_id' => Category::inRandomOrder()->first()->id ?? null,
            'title' => $this->faker->sentence(3),
            'description_ar' => $this->faker->text(200),
            'description_en' => $this->faker->text(200),
            'start_date' => $this->faker->dateTimeBetween(now()->subYears(3), now()->addYear()),
            'min_age' => $this->faker->numberBetween(10, 18),
            'is_paid' => $this->faker->boolean(),
            'is_private' => $this->faker->boolean(),
            'attendance_type' => $this->faker->randomElement(['INVITATION', 'TICKET']),
            'total_cost' => $this->faker->numberBetween(0, 5000),
            'ticket_price' => $this->faker->numberBetween(0, 500),
            'vip_ticket_price' => $this->faker->numberBetween(0, 1000),
            'image' => $this->faker->imageUrl(640, 480, 'events', true, 'event'),
            'rating' => $this->faker->randomFloat(1, 0, 5),
        ];
    }
}
