<?php

namespace App\Helpers;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\Favourite;
use App\Models\Friendship;
use App\Models\VenueReservation;
use Carbon\Carbon;
use DateTime;


class EventHelper
{
public static function getFavouriteStatus($eventId,$userId){
    $isFavourite=Favourite::where('user_id',$userId)
        ->where('event_id',$eventId)->first();
    if($isFavourite)
        return true;
    return  false;
}
public static function putFavouriteStatusInEvent($event,$userId){
    $favouriteStatus=self::getFavouriteStatus($event->id,$userId);
    $eventArray=$event->toArray();
    $eventArray+=['isFavourite'=>$favouriteStatus];
    return $eventArray;
}
    public static function filterEventsByBlockedFriendships($events, $user)
    {
        // Initialize filtered events as an empty collection or array
        $filteredEvents = collect();

        // Check if $events is iterable (array or collection)
        if (is_iterable($events)) {
            $filteredEvents = collect($events)->filter(function ($event) use ($user) {
                $eventCreator = $event->user()->first();
                $friendship = Friendship::where(function ($query) use ($user, $eventCreator) {
                    $query->where('sender_id', $user->id)->where('receiver_id', $eventCreator->id);
                })->orWhere(function ($query) use ($user, $eventCreator) {
                    $query->where('sender_id', $eventCreator->id)->where('receiver_id', $user->id);
                })->first();

                return !$friendship || $friendship->status !== 'BLOCKED';
            });
        } else {
            // Handle single event case
            $event = $events; // Since it's a single event
            $eventCreator = $event->user()->first();
            $friendship = Friendship::where(function ($query) use ($user, $eventCreator) {
                $query->where('sender_id', $user->id)->where('receiver_id', $eventCreator->id);
            })->orWhere(function ($query) use ($user, $eventCreator) {
                $query->where('sender_id', $eventCreator->id)->where('receiver_id', $user->id);
            })->first();

            if (!$friendship || $friendship->status !== 'BLOCKED') {
                $filteredEvents->push($event);
            }
        }

        return $filteredEvents;
    }

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

        return ['currentDateTime' => $currentDateTime, 'eventCreationDate' => $eventCreationDate, 'creationToActionDiff' => $creationToActionDiff, 'startDate' => $eventStartDate, 'endDate' => $eventEndDate, 'dateDifference' => $dateDifference];
    }

    public static function getStartAndEndDateForEvent($eventId)
    {
        $event = Event::find($eventId);
        return ['startDate' => Carbon::parse($event->start_date)->subHours(4)->toDateTimeString(), 'endDate' => Carbon::parse($event->end_date)->addHours(4)->toDateTimeString(),];
    }

    public static function generateSeatNumber($eventId, $ticketType, $venueReservation)
    {
        if ($ticketType === 'VIP') {
            $reservedVipSeats = ResourcesHelper::getNumberOfVipChairReserved($eventId);
            if ($reservedVipSeats === 0) {
                return response()->json(['message' => 'there is no vip seats for this event'], 404);
            }
            $vipCount = $venueReservation->booked_vip_seats;
            $availableSeat = Attendee::where('event_id', $eventId)->where('ticket_type', 'VIP')->where('status', 'CANCELLED')->orderBy('seat_number')->first();

            if ($vipCount >= $reservedVipSeats && !$availableSeat) {
                //  return response()->json(['message'=>'No vip seats available'],400);
                throw new \Exception('No vip seats available');
            }

            if ($availableSeat) {
                return $availableSeat->seat_number;
            } else {

                return 'v' . str_pad($vipCount + 1, 3, '0', STR_PAD_LEFT);
            }
        } else {
            $reservedRegularSeats = ResourcesHelper::getNumberOfRegularChairReserved($eventId);
            if ($reservedRegularSeats === 0) {
                return response()->json(['message' => 'there is no regular seats for this event'], 404);
            }
            $regularCount = $venueReservation->booked_seats;

            $availableSeat = Attendee::where('event_id', $eventId)->where('ticket_type', 'REGULAR')->where('status', 'CANCELLED')->orderBy('seat_number')->first();

            if ($regularCount >= $reservedRegularSeats && !$availableSeat) {
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

    public static function bookSeat($event, $type)
    {
        if ($type === 'VIP') $bookedType = 'booked_vip_seats'; else
            $bookedType = 'booked_seats';
        $venueReservation = VenueReservation::where('event_id', $event->id)->first();
        $bookedSeats = ($venueReservation->$bookedType) + 1;
        $venueReservation->$bookedType = $bookedSeats;
        $venueReservation->save();
    }

    public static function changeUserRating($user, $change)
    {
        $newRating = $user->rating + $change;
        if ($newRating < 0) {
            $newRating = 0;
        } elseif ($newRating > 5) {
            $newRating = 5;
        }
        $user->rating = $newRating;
        $user->save();
    }

    public static function getCreatedEventsCalender($user)
    {
        $days = DateTimeHelper::getFirstDayOfCurrentMonthAndLastDayOfAfterTowMonths();
        $firstDayOfCurrentMonth = $days['firstDayOfCurrentMonth'];
        $lastDayOfTwoMonthsAhead = $days['lastDayOfTwoMonthsAhead'];
        $userCreatedEvents = $user->events()->select('id', 'user_id', 'category_id', 'title', 'start_date', 'min_age', 'is_paid', 'is_private', 'image')->whereBetween('start_date', [$firstDayOfCurrentMonth, $lastDayOfTwoMonthsAhead])->get();
        $userCreatedEvents = $userCreatedEvents->map(function ($event) {
            $eventArray = $event->toArray();
            $eventArray['state'] = 'CREATED';
            return $eventArray;
        });
        return $userCreatedEvents;
    }

    public static function getInvitedOrPurchasedEventsCalender($user, $status)
    {
        $days = DateTimeHelper::getFirstDayOfCurrentMonthAndLastDayOfAfterTowMonths();
        $firstDayOfCurrentMonth = $days['firstDayOfCurrentMonth'];
        $lastDayOfTwoMonthsAhead = $days['lastDayOfTwoMonthsAhead'];

        $statusUpper = ($status === 'invited') ? 'INVITED' : 'PURCHASED';

        $events = $user->attendees()->where('status', $statusUpper)->whereHas('event', function ($query) use ($firstDayOfCurrentMonth, $lastDayOfTwoMonthsAhead) {
                $query->whereBetween('start_date', [$firstDayOfCurrentMonth, $lastDayOfTwoMonthsAhead]);
            })->with(['event' => function ($query) {
                $query->select('id', 'user_id', 'category_id', 'title', 'start_date', 'min_age', 'is_paid', 'is_private', 'image');
            }])->get()->pluck('event'); // Extract only the event objects

        // Map events to add state attribute
        $events = $events->map(function ($event) use ($statusUpper) {
            $eventArray = $event->toArray();
            $eventArray['state'] = $statusUpper;
            return $eventArray;
        });

        return $events;
    }

}
