<?php

namespace App\Helpers;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\VenueReservation;
use Carbon\Carbon;
use DateTime;

class EventHelper
{
    public static function getEventDates($eventId)
    {
        $event = Event::find($eventId);
        if (!$event) {
            return response()->json(['message' => __('event.notFound')], 404);
        }
// Convert currentDateTime to DateTime object
        $currentDateTime = DateTimeHelper::getCurrentDateTime();
        $eventStartDate = new DateTime($event->start_date);
        $eventEndDate = new DateTime($event->end_date);
        $eventCreationDate = new DateTime($event->created_at);
        $dateDifference = $currentDateTime->diff($eventStartDate); //between now and the start date
        $creationToActionDiff = $currentDateTime->diff($eventCreationDate);

        return [
            'currentDateTime' => $currentDateTime,
            'eventCreationDate' => $eventCreationDate,
            'creationToActionDiff' => $creationToActionDiff,
            'startDate' => $eventStartDate,
            'endDate' => $eventEndDate,
            'dateDifference' => $dateDifference];
    }

    public static function getStartAndEndDateForEvent($eventId)
    {
        $event = Event::find($eventId);
        return [
            'startDate' => Carbon::parse($event->start_date)->subHours(4)->toDateTimeString(),
            'endDate' => Carbon::parse($event->end_date)->addHours(4)->toDateTimeString(),
        ];
    }
    public static function generateSeatNumber($eventId, $ticketType,$venueReservation)
    {
        if ($ticketType === 'VIP') {
            $reservedVipSeats = ResourcesHelper::getNumberOfVipChairReserved($eventId);
            if($reservedVipSeats===0){
                return response()->json(['message'=>'there is no vip seats for this event'],404);
            }
            $vipCount =$venueReservation->booked_vip_seats;
            $availableSeat = Attendee::where('event_id', $eventId)
                ->where('ticket_type', 'VIP')
                ->where('status', 'CANCELLED')
                ->orderBy('seat_number')
                ->first();

            if ($vipCount >= $reservedVipSeats&& !$availableSeat) {
              //  return response()->json(['message'=>'No vip seats available'],400);
                throw new \Exception('No vip seats available');
            }

            if ($availableSeat) {
                return $availableSeat->seat_number;
            }else {

                return 'v' . str_pad($vipCount + 1, 3, '0', STR_PAD_LEFT);
            }
            } else {
            $reservedRegularSeats = ResourcesHelper::getNumberOfRegularChairReserved($eventId);
            if($reservedRegularSeats===0){
                return response()->json(['message'=>'there is no regular seats for this event'],404);
            }
            $regularCount =$venueReservation->booked_seats;

            $availableSeat = Attendee::where('event_id', $eventId)
                ->where('ticket_type', 'REGULAR')
                ->where('status', 'CANCELLED')
                ->orderBy('seat_number')
                ->first();

            if ($regularCount >= $reservedRegularSeats&& !$availableSeat) {
               // return response()->json(['message'=>'No regular seats available'],400);
                throw new \Exception('No regular seats available');
            }
            if ($availableSeat) {
                return $availableSeat->seat_number;
            } else {
                return 'r' . str_pad($regularCount + 1, 3, '0', STR_PAD_LEFT);
            }
        }
    }
    public static function bookSeat($event,$type)
    {
        if($type==='VIP')
            $bookedType='booked_vip_seats';
        else
            $bookedType='booked_seats';
        $venueReservation=VenueReservation::where('event_id',$event->id)->first();
        $bookedSeats=($venueReservation->$bookedType)+1;
       $venueReservation->$bookedType=$bookedSeats;
        $venueReservation->save();
    }
}
