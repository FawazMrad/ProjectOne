<?php

namespace App\Http\Controllers;

use App\Helpers\DateTimeHelper;
use App\Helpers\EventHelper;
use App\Models\Attendee;
use App\Models\User;
use Illuminate\Http\Request;

class ScannerController
{
    public function checkIn(Request $request)
    {
        $scannerId = $request->user()->id;
        $attendeeId = $request->input('attendeeId');
        $attendee = Attendee::find($attendeeId);
        $userId = $attendee->user_id;
        $user = User::find($userId);
        if ($attendee) {
            $eventId = $attendee->event_id;
            $isUserScanner = EventHelper::isUserScanner($scannerId, $eventId);
            if ($isUserScanner) {
                if ($attendee->status === 'ATTENDING') return \response()->json(['message' => __('event.userAlreadyCheckedIn')], 400);
                if ($attendee->status != 'PURCHASED') return \response()->json(['message' => __('event.checkedInNotPurchased')], 400);
                $attendee->checked_in = true;
                $attendee->status = 'ATTENDING';
                $attendee->save();
                if ($attendee->ticket_type === 'REGULAR') {
                    EventHelper::changeUserRating($user, 0.1);
                    return \response()->json(['message' => __('event.checkedIn')], 200);
                }
                EventHelper::changeUserRating($user, 0.2);
                return \response()->json(['message' => __('event.checkedInVip')], 200);
            }
            return response()->json(['message' => __('auth.notAttendee')], 400);
        }
        return response()->json(['message' => __('auth.notAttendee')], 400);
    }

    public function makeScanner(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;
        $eventId = $request->input('eventId');
        $newScannerId = $request->input('newScannerId');
        $isUserMainScanner = EventHelper::isUserMainScanner($userId, $eventId);
        if ($isUserMainScanner) {
            $scannerAttendee = Attendee::where('user_id', $newScannerId)
                ->where('event_id', $eventId)
                ->where('status', 'PURCHASED')
                ->first();
            if (!$scannerAttendee) {
                return response()->json(['message' => __('auth.notAttendee')], 400);
            }
            if ($scannerAttendee->is_scanner) {
                return response()->json(['message' => __('auth.alreadyScanner')], 400);
            }
            $scannerAttendee->status = 'ATTENDING';
            $scannerAttendee->checked_in = true;
            $scannerAttendee->is_scanner = true;
            $scannerAttendee->save();
            EventHelper::changeUserRating(User::find($newScannerId), 0.3);
           return response()->json(['message'=>__('auth.newScanner')],200);

        }
        return response()->json(['message' => __('auth.notMainScanner')], 400);
    }
    public function getTodayEvents(Request $request){
        $user=$request->user();
        $userId=$user->id;
        $startDateTimeToday=DateTimeHelper::getDateTime('todayStart');
        $endDateTimeToday=DateTimeHelper::getDateTime('tomorrowEnd');
        $events=$user->events()->where('start_date','>=',$startDateTimeToday)
            ->where('end_date','<=',$endDateTimeToday)
            ->get();
        if($events->isNotEmpty())
            return \response()->json(['events'=>$events],200);
        return \response()->json(['message'=>__('event.noSuchEvents')],404);
    }

}
