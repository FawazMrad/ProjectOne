<?php

namespace App\Http\Controllers;

use App\Helpers\FavouriteHelper;
use App\Models\Event;
use App\Models\Favourite;
use Illuminate\Http\Request;
use function PHPUnit\Framework\throwException;

class FavouriteController
{
    public function addOrRemoveFavourite(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;
        $eventId = $request->input('eventId');

        $isEventFavourite = self::isEventFavourite($user, $eventId);
        if ($isEventFavourite['status']) {
            self::removeFavourite($user, $eventId, $isEventFavourite['favouriteObject']);
            return response()->json(['message' => __('event.removedFromFavourite')], 200);
        }
        self::addFavourite($user, $eventId);
        return response()->json(['message' => __('event.addedToFavourite')], 201);
    }

    public static function isEventFavourite($user, $eventId)
    {
        $userFavourites = $user->favourites;
        foreach ($userFavourites as $favourite) {
            if ($favourite->event_id === $eventId) {
                return [
                    'status' => true,
                    'favouriteObject' => $favourite
                ];
            }
        }
        return ['status' => false];
    }

    public static function removeFavourite($user, $eventId, $favourite)
    {
       $favourite->delete();
    }

    public static function addFavourite($user, $eventId)
    {
        $event=Event::find($eventId);
        $eventCreator=$event->user;

        $isFollowing = $user->following()->where('receiver_id', $eventCreator->id)->exists();
        $isMutual=$user->followers()->where('sender_id',$eventCreator->id)->exists();
        $priorityLevel = ($isFollowing||$isMutual) ? 'HIGH' : 'MID';

        $favourite = Favourite::create([
            'user_id' => $user->id,
            'event_id' => $eventId,
            'priority_level' => $priorityLevel
        ]);
    }

    public function getUserFavourites(Request $request)
    {
        $user = $request->user();
        $month = $request->input('month', false);

        $highPriorityEvents = [];
        $midPriorityEvents = [];

        if ($month) {
            $currentMonth = now()->month;
            $currentYear = now()->year;
            $userFavourites = $user->favourites()
                ->whereHas('event', function ($query) use ($currentMonth, $currentYear) {
                    $query->whereMonth('start_date', $currentMonth)
                        ->whereYear('start_date', $currentYear);
                })
                ->with('event')
                ->get();
        } else {

            $userFavourites = $user->favourites()->with('event')->get();
        }
        $favouritesByPriority=FavouriteHelper::getEventByTheirPriorityLevel($userFavourites);
        return response()->json([
            'highPriorityEvents' => $favouritesByPriority['highPriorityEvents'],
            'midPriorityEvents' => $favouritesByPriority['midPriorityEvents']
        ], 200);
           }


}
