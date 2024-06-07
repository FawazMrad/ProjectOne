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

class ResourcesHelper{
    public static function getStartAndEndDateForEvent($eventId)
    {
        $event = Event::find($eventId);
        return [
            'startDate' => $event->start_date,
            'endDate' => $event->end_date

        ];
    }
    public function getCost($resource,$resourceId,$quantity){
       $individualCost= "App\\Models\\$resource"::where('id',$resourceId)->get('cost')->first();
       $totalCost=$individualCost*$quantity;
       return $totalCost;

    }


}
