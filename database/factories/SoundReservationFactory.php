<?php

namespace Database\Factories;

use App\Models\Sound;
use App\Models\Event;
use App\Models\SoundReservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class SoundReservationFactory extends Factory
{
    protected $model = SoundReservation::class;

    public function definition()
    {
        $sound = Sound::inRandomOrder()->first();

        return [
            'sound_id' => $sound->id,
            'cost' => $sound->cost,
        ];
    }
}
