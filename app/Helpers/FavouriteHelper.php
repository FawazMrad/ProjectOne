<?php

namespace App\Helpers;

class FavouriteHelper
{
    public static function getEventByTheirPriorityLevel($userFavourites)
    {
        $highPriorityEvents=[];
        $midPriorityEvents=[];
        foreach ($userFavourites as $favourite) {
            if ($favourite->priority_level === 'HIGH') {
                $highPriorityEvents[] = $favourite->event;
            } elseif ($favourite->priority_level === 'MID') {
                $midPriorityEvents[] = $favourite->event;
            }
        }
        if ($highPriorityEvents || $midPriorityEvents) {
            return ['status'=>true,'highPriorityEvents' => $highPriorityEvents, 'midPriorityEvents' => $midPriorityEvents];
        }
        return ['status'=>false,'message' => __('event.NoFav'), 404];

    }

}
