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
      $currentDateTime = DateTimeHelper::getCurrentDateTime();
      $eventStartDate = new DateTime($event->start_date);
      $eventEndDate = new DateTime($event->end_date);
      $eventCreationDate=new DateTime($event->created_at);
      $dateDifference = $currentDateTime->diff($eventStartDate); //between now and the start date
      $creationToActionDiff = $currentDateTime->diff($eventCreationDate);

      return [
          'currentDateTime'=>$currentDateTime,
          'eventCreationDate'=>$eventCreationDate,
          'creationToActionDiff'=>$creationToActionDiff,
          'startDate'=>$eventStartDate,
          'endDate'=>$eventEndDate,
          'dateDifference'=>$dateDifference];
  }
    public static function getStartAndEndDateForEvent($eventId)
    {
        $event = Event::find($eventId);
        return [
            'startDate' => Carbon::parse($event->start_date)->subHours(4)->toDateTimeString(),
            'endDate' => Carbon::parse($event->end_date)->addHours(4)->toDateTimeString(),
        ];
    }
}
