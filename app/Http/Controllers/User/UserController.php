<?php

namespace App\Http\Controllers\User;

use App\Helpers\DateTimeHelper;
use App\Helpers\QR_CodeHelper;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController
{
    public function getUser(Request $request)
    {
        $fields = ['id', 'first_name', 'last_name', 'email', 'rating', 'rating', 'qr_code', 'followers', 'following'];

        $user = User::select($fields)->find($request->input('userId'));

        if ($user)
            return response()->json(['user' => $user], 200);
        return response()->json(['message' => 'user not found'], 404);
    }
    public function getProfile(Request $request)
    {
        $user = $request->user();
        return response()->json(['user' => $user], 200);
    }
    public function getAttendedEvents(Request $request)
    {
        $user = $request->user();
        $attendedEvents = $user->attendees()
            ->where('checked_id', true)->get();
        if ($attendedEvents->inNotEmpty())
            return response()->json(['AttendedEvents' => $attendedEvents], 200);
        return response()->json(['message' => __('event.noSuchEvents')], 404);
    }
    public function eventsHistory(Request $request)
    {
        $user = $request->user();
        $currentDateTime = DateTimeHelper::getCurrentDateTime();
        $events = $user->events()
            ->where('start_date', '<', $currentDateTime)
            ->get();

        if ($events->isNotEmpty()) {
            return response()->json(['createdEvents' => $events], 200);
        }

        return response()->json(['message' => __('event.noSuchEvents')], 404);
    }
    public function getUpComingEvents(Request $request)
    {
        $user = $request->user();
        $currentDateTime = DateTimeHelper::getCurrentDateTime();
        $upComingEvents = $user->events()
            ->where('start_date', '>', $currentDateTime)->get();
        if ($upComingEvents->isNotEmpty()) {
            return response()->json(['upComingEvents' => $upComingEvents], 200);
        }

        return response()->json(['message' => __('event.noSuchEvents')], 404);
    }
    public function searchUsers(Request $request)
    {
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
            $query->where('phone_number', $phoneNumber );
        }

        if ($request->has('minRating')) {
            $query->where('rating', '>=', $request->input('minRating'));
        }

        $users = $query->get();

        if ($users->isNotEmpty()) {
            return response()->json(['users' => $users], 200);
        }

        return response()->json(['message' => __('auth.userNotFound')], 404);
    }
    public function editProfile(Request $request){
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
        $qrData=[
            'id'=>$user->id,
            'user name'=>" $user->first_name  $user->last_name",
            'phone number'=>$user->phone_number,
            'user email'=>$user->email
        ];
        QR_CodeHelper::generateAndSaveQrCode($qrData,'User');
        return response()->json(['user' => $user], 200);
    }
}
