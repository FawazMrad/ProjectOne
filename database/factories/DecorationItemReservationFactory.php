<?php

namespace Database\Factories;

use App\Models\DecorationItem;
use App\Models\Event;
use App\Models\DecorationItemReservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class DecorationItemReservationFactory extends Factory
{
    protected $model = DecorationItemReservation::class;

    public function definition()
    {
        // Select a random existing event
        $decorationItem = DecorationItem::inRandomOrder()->first();

        return [
            'decoration_item_id' => $decorationItem->id,
            'quantity' => $this->faker->numberBetween(1, $decorationItem->quantity),
            'cost' => $this->faker->numberBetween(10, $decorationItem->quantity * $decorationItem->cost),
        ];
    }
}
