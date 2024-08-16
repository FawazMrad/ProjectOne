<?php

namespace Database\Factories;

use App\Models\Security;
use App\Models\Event;
use App\Models\SecurityReservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class SecurityReservationFactory extends Factory
{
    protected $model = SecurityReservation::class;

    public function definition()
    {
        $security = Security::inRandomOrder()->first();

        return [
            'security_id' => $security->id,
            'quantity' => $this->faker->numberBetween(1, $security->quantity),
            'cost' => $this->faker->numberBetween(1, $security->quantity * $security->cost),
        ];
    }
}
