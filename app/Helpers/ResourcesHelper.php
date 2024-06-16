<?php

namespace App\Helpers;

use App\Models\Event;
use App\Models\Venue;
use App\Models\Furniture;
use App\Models\DecorationItem;
use App\Models\Sound;
use App\Models\Drink;
use App\Models\Food;
use App\Models\Security;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class ResourcesHelper{


    public static function getStartAndEndDateForEvent($eventId)
    {
        $event = Event::find($eventId);
        return [
            'startDate' => Carbon::parse($event->start_date)->subHours(4)->toDateTimeString(),
            'endDate' => Carbon::parse($event->end_date)->addHours(4)->toDateTimeString(),
        ];
    }
    public static function getCost($resource, $resourceId, $quantity)
    {
        // Fetch the model instance
        $resourceModel = "App\\Models\\$resource"::find($resourceId);

    // Check if the model instance exists
    if (!$resourceModel) {
        throw new \Exception("Resource not found");
    }

    // Get the individual cost
    $individualCost = $resourceModel->cost;

    // Calculate the total cost
    $totalCost = $individualCost * $quantity;

    return $totalCost;
}

}
