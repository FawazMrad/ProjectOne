<?php

namespace App\Helpers;

class FavouriteHelper
{
    public static function getEventByTheirPriorityLevel($userFavourites)
    {
        foreach ($userFavourites as $favourite) {
            if ($favourite->priority_level === 'HIGH') {
                $highPriorityEvents[] = $favourite->event;
            } elseif ($favourite->priority_level === 'MID') {
                $midPriorityEvents[] = $favourite->event;
            }
}
  return [
      'highPriorityEvents'=>$highPriorityEvents,
      'midPriorityEvents'=>$midPriorityEvents
  ]    ;
}

}
