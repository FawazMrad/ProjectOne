<?php

namespace App\Helpers;

use App\Models\Event;
use App\Models\FurnitureReservation;
use App\Models\Venue;
use App\Models\Furniture;
use App\Models\DecorationItem;
use App\Models\Sound;
use App\Models\Drink;
use App\Models\Food;
use App\Models\Security;
use App\Models\VenueReservation;
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
    public static function getNumberOfRegularChairReserved($eventId)
    {
        // Retrieve the event
        $event = Event::find($eventId);
        $numberOfChairs = 0;

        if ($event) {
            $eventReservations = FurnitureReservation::where('event_id', $eventId)
                ->join('furniture', 'furniture.id', '=', 'furniture_reservations.furniture_id')
                ->where('furniture.type', 'LIKE', '%_regularChair')
                ->get(['furniture_reservations.quantity']);

            foreach ($eventReservations as $reservation) {
                $numberOfChairs += $reservation->quantity;
            }
        }
        return $numberOfChairs;
    }
    public static function getNumberOfVipChairReserved($eventId)
    {
        // Retrieve the event
        $event = Event::find($eventId);
        $numberOfVipChairs = 0;

        if ($event) {
            $eventReservations = FurnitureReservation::where('event_id', $eventId)
                ->join('furniture', 'furniture.id', '=', 'furniture_reservations.furniture_id')
                ->where('furniture.type', 'LIKE', '%_vipChair')
                ->get(['furniture_reservations.quantity']);

            foreach ($eventReservations as $reservation) {
                $numberOfVipChairs += $reservation->quantity;
            }
        }
        return $numberOfVipChairs;
    }



    public static function getVenueCapacity($eventId){
        $event = Event::find($eventId);
        $capacity=VenueReservation::where('event_id',$eventId)
            ->join('venues','venues.id','=','venue_reservations.venue_id')
            ->first(['venues.max_capacity_no_chairs'])->max_capacity_no_chairs;

        return $capacity;
    }


}
