<?php

namespace App\Http\Controllers;


use App\Helpers\DateTimeHelper;
use App\Helpers\EventHelper;
use App\Helpers\QR_CodeHelper;
use App\Http\Controllers\User\FriendshipController;
use App\Http\Controllers\User\UserController;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use App\Models\VenueReservation;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendeeController
{
    public function searchUsersToInvite(Request $request){
        $user=$request->user();
        $eventId=$request->input('eventId');
        $event=Event::find($eventId);
        $eventAttendeesIds = $event->attendees()->pluck('user_id')->toArray();

        $users=UserController::searchUsersStatic($request)['users'];
        if(!$users )
            return response()->json(['Users'=>0],404);
        $filteredUsers=self::filterInvitedUsers($users,$eventAttendeesIds);
        if($filteredUsers)
            return response()->json(['Users'=>$filteredUsers],200);
        return response()->json(['Users'=>$filteredUsers],404);
    }
    public static function filterInvitedUsers($users,$eventAttendees){
        $usersToInvite = [];
        foreach ($users as $user) {
            if (!in_array($user->id, $eventAttendees)) {
                $usersToInvite[] = $user;
            }
        }
        return $usersToInvite;
    }
    public function getFollowingToInvite(Request $request)
    {
        $user = $request->user();
        $eventId = $request->input('eventId');
        $event = Event::find($eventId);

        $following = FriendshipController::getFollowingStatic($user->id); // Following  FollowingNumber

        $eventAttendees = $event->attendees()->pluck('user_id')->toArray();
        $followingToInvite = [];
        $followingToInvite =self::filterInvitedUsers($following['Following'],$eventAttendees);

        if($followingToInvite)
        return response()->json(['Following'=>$followingToInvite],200);
        return response()->json(['Following'=>$followingToInvite],404);
    }
    public function getInvitedUsers(Request $request)
    {
        $user = $request->user();
        $eventId = $request->input('eventId');
        $event = Event::find($eventId);

        // Check if the event exists
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $attendees = $event->attendees()->with('user')->get();

        // Map over the attendees and include the user's full name within the attendee data
        $invitedUsers = $attendees->map(function ($attendee) {
            // Add the user's full name to the attendee data
            $attendeeData = $attendee->only([
                'id',
                'user_id',
                'event_id',
                'status',
                'checked_in',
                'purchase_date',
                'ticket_price',
                'ticket_type',
                'seat_number',
                'discount',
                'qr_code',
                'is_main_scanner',
                'is_scanner',
                'created_at',
                'updated_at'
            ]);
            $attendeeData['userFullName'] = $attendee->user->first_name . ' ' . $attendee->user->last_name;

            return $attendeeData;
        });

        if ($invitedUsers->isNotEmpty()) {
            return response()->json(['invitedUsers' => $invitedUsers], 200);
        }

        return response()->json(['invitedUsers' => 0], 404);
    }
    public function sendInvitation(Request $request)
    {
        $userId = $request->input('userId');
        $user = User::find($userId);
        $eventId = $request->input('eventId');
        $event = Event::find($eventId);
        $userAge = DateTimeHelper::userAge($user->birth_date);
        if ($userAge < $event->min_age) {
            return response()->json(['message' => __('auth.userUnderAge')], 400);
        }
        $dates = EventHelper::getEventDates($eventId);

        if ($dates['dateDifference']->days <= 1) {
            return response()->json(['message' => __('event.CannotInviteNow')], 400);
        }

        $ticketType = $request->input('ticketType');

        if ($ticketType === 'VIP') {
            $ticketTypePrice = 'vip_ticket_price';
        } else {
            $ticketTypePrice = 'ticket_price';
        }
        $existingInvitation = Attendee::where('user_id', $userId)->where('event_id', $eventId)->first();

        if ($existingInvitation) {
            return response()->json(['message' => __('event.AlreadyInvited')], 400);
        }
        DB::beginTransaction();
        try {
            //$venueReservation=VenueReservation::where('event_id',$eventId)->first();
            $venueReservation = $event->venueReservation;
            $seatNumber = EventHelper::generateSeatNumber($eventId, $ticketType, $venueReservation);
            $attendee = Attendee::create(['user_id' => $userId, 'event_id' => $eventId, 'status' => 'INVITED', 'ticket_type' => $ticketType, 'ticket_price' => $event->$ticketTypePrice, 'seat_number' => $seatNumber, 'discount' => 0, 'qr_code' => 'startingValue']);

            $attendee->save();

            $qrData = ['id' => $attendee->id, 'userId' => $userId, 'eventId' => $eventId, 'seatNumber' => $seatNumber];
            $qrCode = QR_CodeHelper::generateAndSaveQrCode($qrData, 'Attendee');
            DB::commit();
            if ($ticketType === 'VIP') {// book one more chair in the venue reservation
                EventHelper::bookSeat($event, $ticketType);
            } else {
                EventHelper::bookSeat($event, $ticketType);
            }
            return response()->json(['message' => 'Invitation sent'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function confirmInvitation(Request $request)
    {
        $userId = $request->user()->id;
        $attendeeId = $request->input('attendeeId');

        $attendee = Attendee::find($attendeeId);
        $event = $attendee->event()->get();
        if ($attendee->status !== 'INVITED') {
            return response()->json(['message' => 'Invitation cannot be confirmed'], 400);
        }
        if ($event->is_paid) {
            $withdrawResult = WalletController::giftStatic($userId, $event->user_id, $attendee->ticket_price);
            if ($withdrawResult['status'] === true) {
                $attendee->update(['status' => 'PURCHASED']);
                $attendee->purchase_date = DateTimeHelper::getCurrentDateTime();
                $attendee->save();
                return response()->json(['message' => __('event.confirmInvitation')], 201);
            }
            return response()->json(['message' => __('event.errorInConfirmInvitation'), 'withdraw error' => $withdrawResult['message']], 400);
        } else {
            $attendee->update(['status' => 'PURCHASED']);
            $attendee->purchase_date = DateTimeHelper::getCurrentDateTime();
            $attendee->save();
        }
        return response()->json(['message' => 'Invitation confirmed'], 200);
    }

    public function cancelInvitation(Request $request)
    {
        $attendeeId = $request->input('attendeeId');
        $attendee = Attendee::find($attendeeId);
        if ($attendee->status != 'INVITED') {
            return response()->json(['message' => 'Attendee not invited .'], 400);
        }
        $attendee->status = 'CANCELLED';
        $attendee->save();
        return response()->json(['message' => __('event.CancelInvitation')], 200);

    }

    public function purchaseTicket(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;
        $eventId = $request->input('eventId');
        $event = Event::find($eventId);
        $userAge = DateTimeHelper::userAge($user->birth_date);
        if ($userAge < $event->min_age) {
            return response()->json(['message' => __('auth.userUnderAge')], 400);
        }

        $isUserAttend = Attendee::where('user_id', $userId)->where('event_id', $eventId)->first();
//        if($isUserAttend){
//            if($isUserAttend->is_main_scanner)
//                return \response()->json(['message'=>__('auth.creatorCannotPurchase')],403);
//        }
        if ($isUserAttend) if ($isUserAttend->status !== 'CANCELLED') return response()->json(['message' => __('event.userAlreadyAttend')], 400);
        $dates = EventHelper::getEventDates($eventId);
        if ($dates['dateDifference']->days > 5) {
            return response()->json(['message' => __('event.CannotPurchaseNow')], 400);
        }
        $event = Event::find($eventId);
        $status = 'PURCHASED';
        DB::beginTransaction();
        try {
            $purchaseDate = DateTimeHelper::getCurrentDateTime();
            $ticketType = $request->input('ticketType');
            if ($ticketType === 'VIP') {
                $ticketTypePrice = 'vip_ticket_price';
            } else {
                $ticketTypePrice = 'ticket_price';
            }
            $ticketPrice = $event->$ticketTypePrice;
            $venueReservation = $event->venueReservation()->first();
            $seatNumber = EventHelper::generateSeatNumber($eventId, $ticketType, $venueReservation);

            $attendee = Attendee::create(['user_id' => $userId, 'event_id' => $eventId, 'status' => $status, 'purchase_date' => $purchaseDate, 'ticket_price' => $ticketPrice, 'ticket_type' => $ticketType, 'seat_number' => $seatNumber, 'discount' => 0, 'qr_code' => 'still without qr']);
            $attendee->save();
            $qrData = ['id' => $attendee->id, 'userId' => $userId, 'eventId' => $eventId, 'seatNumber' => $seatNumber];
            $qrCode = QR_CodeHelper::generateAndSaveQrCode($qrData, 'Attendee');

            if ($event->is_paid) {
                $withdrawResult = WalletController::giftStatic($userId, $event->user_id, $ticketPrice);
                if ($withdrawResult['status'] === true) {
                    $attendee->update(['status' => 'PURCHASED']);
                    $attendee->purchase_date = DateTimeHelper::getCurrentDateTime();
                    $attendee->save();
                    DB::commit();
                    return response()->json(['message' => __('event.purchaseDone')], 201);
                }
                DB::rollBack();
                return response()->json(['message' => __('event.errorInPurchaseInvitation'), 'withdraw error' => $withdrawResult['message']], 400);
            } else {
                $attendee->update(['status' => 'PURCHASED']);
                $attendee->purchase_date = DateTimeHelper::getCurrentDateTime();
                $attendee->save();
                DB::commit();
                return response()->json(['message' => __('event.purchaseDone')], 201);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function cancelTicket(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;
        $attendeeId = $request->input('attendeeId');
        $attendee = Attendee::find($attendeeId);
        if ($attendee->status === 'CANCELLED') return \response()->json(['message' => __('event.AlreadyCancelled')], 400);
        if ($attendee->status != 'Purchased') return \response()->json(['message' => __('event.AlreadyCancelled')], 400);
        $event = $attendee->event()->first();
        if ($event->is_paid) {
            WalletController::refund($userId, $event->user_id, $attendee->ticket_price);
        }
        $attendee->status = 'CANCELLED';
        $attendee->save();
        $userRating = $user->rating;
        if ($userRating <= 2.5) {
            $user->rating = 0;
        } else {
            $newUserRating = $userRating - 0.25;
            $user->rating = $newUserRating;
        }
        $user->save();
        return response()->json(['message' => __('event.cancelTicket')], 201);
    }

}
