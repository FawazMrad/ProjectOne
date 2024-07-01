<?php

namespace App\Helpers;
use App\Models\Event;
use Carbon\Carbon;
use DateTime;

class EventHelper
{
  public static function getEventDates($eventId){


      $event = Event::find($eventId);

      if (!$event) {
          return response()->json(['message' => __('event.notFound')], 404);
      }
// Convert currentDateTime to DateTime object
      $currentDateTime = new DateTime (Carbon::now());
      $eventStartDate = new DateTime($event->start_date);
      $eventCreationDate=new DateTime($event->created_at);
      $dateDifference = $currentDateTime->diff($eventStartDate); //between now and the start date
      $creationToActionDiff = $currentDateTime->diff($eventCreationDate);

      return ['currentDateTime'=>$currentDateTime,
          'eventCreationDate'=>$eventCreationDate,
          'creationToActionDiff'=>$creationToActionDiff,
             'eventStartDate'=>$eventStartDate,
          'dateDifference'=>$dateDifference];
  }
}
