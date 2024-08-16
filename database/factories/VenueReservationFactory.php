<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Venue;
use App\Models\VenueReservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class VenueReservationFactory extends Factory
{
    protected $model = VenueReservation::class;

    public function definition()
    {
        $venue = Venue::inRandomOrder()->first();

        return [
            'venue_id' => $venue->id,
            'booked_seats' => $this->faker->numberBetween(0, $venue->max_capacity_no_chairs),
            'booked_vip_seats' => $this->faker->numberBetween(0, $venue->vip_chairs),
            'cost' => $venue->cost,
        ];
    }
}
