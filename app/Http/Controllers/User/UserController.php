<?php

namespace App\Http\Controllers\User;

use App\Helpers\DateTimeHelper;
use App\Helpers\EventHelper;
use App\Helpers\QR_CodeHelper;
use App\Models\Friendship;
use App\Models\User;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController
{
    public function getInvitations(Request $request)
    {
        $user = $request->user();
        $currentDateTime = Carbon::now();
        $inviteType = $request->header('type');
        switch ($inviteType) {
            case 'INVITED':
                $invitations = $user->attendees()->where('status', 'INVITED');
                break;

            case 'OTHER':
                $invitations = $user->attendees()
                    ->where(function($query) {
                        $query->where('status', 'CANCELLED')
                            ->orWhere('status', 'PURCHASED');
                    });
                break;

        }
            $invitations=$invitations->whereHas('event', function ($query) use ($currentDateTime) {
                $query->where('start_date', '>=', $currentDateTime);
            })->get(['id', 'status', 'event_id', 'qr_code', 'user_id', 'checked_in', 'purchase_date', 'ticket_price', 'seat_number', 'discount', 'is_main_scanner', 'is_scanner']);

        if ($invitations->isNotEmpty()) {
            return response()->json(['invitations' => $invitations], 200);
        }

        return response()->json(['message' => __('event.noInvitations')], 404);
    }

    public function getUser(Request $request)
    {
        $user = $request->user();
        $userBirthDate = $user->birth_date;
        $fields = ['id', 'first_name', 'last_name', 'email', 'rating', 'qr_code', 'followers', 'following','created_at'];
        $desiredUser = User::select($fields)->find($request->input('userId'));

        if ($desiredUser) {
            $friendship = Friendship::where(function ($query) use ($user, $desiredUser) {
                $query->where('sender_id', $user->id)->where('receiver_id', $desiredUser->id);
            })->orWhere(function ($query) use ($user, $desiredUser) {
                $query->where('sender_id', $desiredUser->id)->where('receiver_id', $user->id);
            })->first();

            $friendshipStatus = 'NOT_FOLLOWING';

            if ($friendship) {
                if ($friendship->status == 'BLOCKED') {
                    $friendshipStatus = 'BLOCKED';
                    return response()->json(['message' => 'User not found'], 404);
                } elseif (($friendship->status == 'FOLLOWING' && $friendship->sender_id === $user->id) ) {
                    $friendshipStatus = 'FOLLOWING';
                }elseif( $friendship->status == 'MUTUAL'){
                    $friendshipStatus = 'MUTUAL';
                }

            }
            $age = DateTimeHelper::userAge($userBirthDate);
            $userArray=$desiredUser->toArray();
            $userArray+=['friendShipStatus'=>$friendshipStatus];
            $userArray+=['age'=>$age];
            return response()->json(['user' => $userArray], 200);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user();
        $userBirthDate = $user->birth_date;
        $age = DateTimeHelper::userAge($userBirthDate);
        $userArray = $user->toArray();
        $userArray += ['age' => $age];
        return response()->json(['user' => $userArray], 200);
    }

    public function getAttendedEvents(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;
        $attendedEvents = $user->attendees()->where('checked_in', true)->get();
        if ($attendedEvents->isNotEmpty()) {
            $filteredEvents = $attendedEvents->map(function ($filteredEvent) use ($userId) {
                return EventHelper::putFavouriteStatusInEvent($filteredEvent, $userId);
            });
            return response()->json(['AttendedEvents' => $filteredEvents], 200);
        }
        return response()->json(['message' => __('event.noSuchEvents')], 404);
    }

    public function eventsCreatedHistory(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;
        $currentDateTime = DateTimeHelper::getCurrentDateTime();
        $endDateTime = clone $currentDateTime;
        $endDateTime->modify('+6 days');
        $events = $user->events()->where('start_date', '<', $endDateTime)->get();

        if ($events->isNotEmpty()) {
            $filteredEvents = $events->map(function ($filteredEvent) use ($userId) {
                return EventHelper::putFavouriteStatusInEvent($filteredEvent, $userId);
            });
            return response()->json(['createdEvents' => $filteredEvents], 200);
        }

        return response()->json(['message' => __('event.noSuchEvents')], 404);
    }

    public function getCreatedUpdatableEvents(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;
        $currentDateTime = DateTimeHelper::getCurrentDateTime();
        $endDateTime = clone $currentDateTime;
        $endDateTime->modify('+6 days');
        $upComingEvents = $user->events()->where('start_date', '>=', $endDateTime)->get();
        if ($upComingEvents->isNotEmpty()) {
            $filteredEvents = $upComingEvents->map(function ($filteredEvent) use ($userId) {
                return EventHelper::putFavouriteStatusInEvent($filteredEvent, $userId);
            });
            return response()->json(['upComingEvents' => $filteredEvents], 200);
        }

        return response()->json(['message' => __('event.noSuchEvents')], 404);
    }
 public function searchUsers(Request $request){
        $user=$request->user();
        $targetUsers=self::searchUsersStatic($request);
        if($targetUsers['statusCode']===404){
            return response()->json(['message' => $targetUsers['message']],$targetUsers['statusCode']);
        }
     return response()->json(['users' => $targetUsers['users']], $targetUsers['statusCode']);

 }

    public static function searchUsersStatic($request)
    {
        $user = $request->user();


        $query = User::query();

        if ($request->has('name')) {
            $name = $request->input('name');
            $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', '%' . $name . '%');
        }

        if ($request->has('email')) {
            $email = $request->input('email');
            $query->where('email', 'LIKE', '%' . $email . '%');
        }

        if ($request->has('phoneNumber')) {
            $phoneNumber = $request->input('phoneNumber');
            $query->where('phone_number', $phoneNumber);
        }

        if ($request->has('minRating')) {
            $query->where('rating', '>=', $request->input('minRating'));
        }

        $users = $query->get();

        if ($users->isNotEmpty()) {
            $filteredUsers = $users->filter(function ($filteredUser) use ($user) {
                $friendship = Friendship::where(function ($query) use ($user, $filteredUser) {
                    $query->where('sender_id', $user->id)->where('receiver_id', $filteredUser->id);
                })->orWhere(function ($query) use ($user, $filteredUser) {
                    $query->where('sender_id', $filteredUser->id)->where('receiver_id', $user->id);
                })->first();

                return !$friendship || $friendship->status !== 'BLOCKED';
            });

            if ($filteredUsers->isNotEmpty()) {
                return ['users' => $filteredUsers->values(),'statusCode'=> 200];
            }
        }

        return ['message' => __('auth.userNotFound'),'statusCode'=> 404];
    }

    public function editProfile(Request $request)
    {
        $user = $request->user();
        if ($request->has('firstName')) {
            $user->first_name = $request->input('firstName');
        }

        if ($request->has('lastName')) {
            $user->last_name = $request->input('lastName');
        }

        if ($request->has('address')) {
            $user->address = $request->input('address');
        }

        if ($request->has('birthDate')) {
            $user->birth_date = $request->input('birthDate');
        }

        if ($request->has('phoneNumber')) {
            $user->phone_number = $request->input('phoneNumber');
        }

        if ($request->has('profilePicture')) {
            $user->profile_pic = $request->input('profilePicture');
        }
        if ($request->has('email')) {
            $user->email = $request->input('email');
        }
        $user->save();
        $qrData = ['id' => $user->id, 'user name' => " $user->first_name  $user->last_name", 'phone number' => $user->phone_number, 'user email' => $user->email];
        QR_CodeHelper::generateAndSaveQrCode($qrData, 'User');
        return response()->json(['user' => $user], 200);
    }

    public function resetPassword(Request $request)
    {
        $user = $request->user();
        $oldPassword = $request->input('oldPassword');
        $newPassword = $request->input('newPassword');
        $givenCurrentPassword = Hash::check($oldPassword, $user->password);
        if ($givenCurrentPassword) {
            $user->password = Hash::make($newPassword);
            $user->save();
            return \response()->json(['message' => __('auth.resetPassword')], 200);
        }
        return \response()->json(['message' => __('auth.password')], 400);
    }

}
