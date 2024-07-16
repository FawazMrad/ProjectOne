<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Rating;
use App\Models\Sound;
use Exception;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function RateEvent(Request $request)
    {
        $validatedData = $request->validate([
            'event_id' => 'required|integer',
            'venue_rating' => 'nullable|numeric|min:0|max:5',
            'decor_rating' => 'nullable|numeric|min:0|max:5',
            'music_rating' => 'nullable|numeric|min:0|max:5',
            'food_rating' => 'nullable|numeric|min:0|max:5',
            'drink_rating' => 'nullable|numeric|min:0|max:5'
        ]);
        $validFields = ['venue_rating', 'decor_rating', 'music_rating', 'food_rating', 'drink_rating'];
        $eventId = $request->input('event_id');
        $event = Event::find($eventId);

        if (!$event) {
            return response()->json(['message' => __('event.eventNotFound')], 404);
        }

        $user = $request->user();
        $userId = $user->id;
        $userAlreadyRated=$user->ratings()
            ->where('event_id',$eventId)
            ->exists();

        $userIsAttended = $user->attendees()
            ->where('event_id', $eventId)
            ->where('checked_in', true)
            ->exists();
          if($userAlreadyRated)
              return response()->json(['message'=>__('event.alreadyRated')],400);
        if ($userIsAttended) {

            if ($request->has('music_rating')) {
                self::updateSoundRating($request->input('music_rating'), $eventId);
            }
            if ($request->has('venue_rating')) {
                $venueReservation = $event->venueReservation()->first();
                if ($venueReservation) {
                    self::updateReservationRating($request->input('venue_rating'), $venueReservation->venue_id, 'Venue');
                }
            }
            $rating = new Rating();
            $rating->event_id = $eventId;
            $rating->user_id = $userId;

            // Initialize variables for calculating the average
            $totalRating = 0;
            $ratingCount = 0;

            // Iterate through the valid fields and assign values
            foreach ($validFields as $field) {
                if ($request->has($field)) {
                    $value = $request->input($field);
                    $rating->$field = $value;

                    if ($value !== null) {
                        $totalRating += $value;
                        $ratingCount++;
                    }
                } else {
                    $rating->$field = null;
                }
            }

            if ($ratingCount > 0) {
                $aggregateRating = $totalRating / $ratingCount;
                $rating->aggregate_rating=$aggregateRating;
                self::updateReservationRating($aggregateRating, $eventId, 'Event');
            } else {
                $rating->aggregate_rating = null;
            }
            $rating->save();
            return response()->json(['message' => __('event.rateDone')], 400);
        }
        return response()->json(['message' => 'this user does not attend this event'], 400);
    }

    public static function updateSoundRating($newRating, $eventId)
    {
        $event = Event::find($eventId);
        $soundIds = $event->soundReservations()
            ->pluck('sound_id');

        foreach ($soundIds as $soundId) {
            self::updateReservationRating($newRating, $soundId, 'Sound');
        }
    }

    public static function updateReservationRating($newRating, $reservationId, $reservationModelName)
    {
        $modelClass = "App\\Models\\$reservationModelName";
        $modelObject = $modelClass::find($reservationId);

        if ($modelObject) {
            $currentRating = $modelObject->rating;
            $updatedRating = ($currentRating + $newRating) / 2;
            $modelObject->rating = min($updatedRating, 5); // Ensure rating does not exceed 5
            $modelObject->save();
        }
    }
}
