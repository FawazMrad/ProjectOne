<?php

namespace App\Http\Controllers;

use App\Helpers\ResourcesHelper;
use App\Models\DecorationItemReservation;
use App\Models\DrinkReservation;
use App\Models\FoodReservation;
use App\Models\FurnitureReservation;
use App\Models\Security;
use App\Models\SecurityReservation;
use App\Models\SoundReservation;
use App\Models\Venue;
use App\Models\VenueReservation;
use LanguageDetection\Language;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Helpers\TranslationHelper;
use Stichoza\GoogleTranslate\GoogleTranslate;


class EventController extends Controller
{
    public function storeStep1(Request $request)
    {
        $validatedData = $request->validate([
            'userId' => 'required|exists:users,id',
            'categoryId' => 'required|exists:categories,id',
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'minAge' => 'required|integer|min:0',
            'isPaid' => 'required|boolean',
            'isPrivate' => 'required|boolean',
            'attendanceType' => 'required|in:invitation,ticket',
            'image' => 'nullable|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',

        ]);


        //translate the description
        $validatedData = TranslationHelper::descriptionAndTranslatedDescription($validatedData);

        // Create the event
        $event = Event::create([
            'user_id' => $validatedData['userId'],
            'category_id' => $validatedData['categoryId'],
            'title' => $validatedData['title'],
            'description_ar' => $validatedData['description_ar'],
            'description_en' => $validatedData['description_en'],
            'min_age' => $validatedData['minAge'],
            'is_paid' => $validatedData['isPaid'],
            'is_private' => $validatedData['isPrivate'],
            'attendance_type' => $validatedData['attendanceType'],
            'image' => $validatedData['image'],
            'start_date' => $validatedData['startDate'],
            'end_date' => $validatedData['endDate'],
        ]);
if($event)
        return response()->json(['message' => __('event.completeStepOne'), 'eventId' => $event->id,], 201);
        return response()->json(['message' => __('event.errorIncompleteStepOne'), 'eventId' => $event->id,], 400);

    }

    public function storeStep2(Request $request)
    {
        $totalCost = 0;
        $eventId = $request->input('Venue')->eventId;
        //RequestInfo
        $categoryInfo = $request->input('Category');
        $venueInfo = $request->input('Venue');
        $furnitureInfo = $request->input('Furniture');
        $decorationItemInfo = $request->input('DecorationItem');
        $soundInfo = $request->input('Sound');
        $securityInfo = $request->input('Security');
        $foodInfo = $request->input('Food');
        $drinkInfo = $request->input('Drink');



        $venueCost=ResourcesHelper::getCost('Venue', $venueInfo['id'], 1);
        $venueReservation = VenueReservation::create([
            "venue_id" => $venueInfo['id'],
            "event_id" => $venueInfo['eventId'],
            "start_date" => $venueInfo['startDate'],
            "end_date" => $venueInfo['endDate'],
            "cost" => $venueCost
        ]);
        $furnitureCost=ResourcesHelper::getCost('Furniture', $furnitureInfo['id'], $furnitureInfo['quantity']);
        $furnitureReservation = FurnitureReservation::create([
            "furniture_id" => $furnitureInfo['id'],
            "event_id" => $furnitureInfo['eventId'],
            "start_date" => $furnitureInfo['startDate'],
            "end_date" => $furnitureInfo['endDate'],
            "quantity" => $furnitureInfo['quantity'],
            "cost" => $furnitureCost
        ]);
        $decorationItemCost=ResourcesHelper::getCost('DecorationItem', $decorationItemInfo['id'], $decorationItemInfo['quantity']);
        $decorationItemReservation = DecorationItemReservation::create([
            "decoration_item_id" => $decorationItemInfo['id'],
            "event_id" => $decorationItemInfo['eventId'],
            "start_date" => $decorationItemInfo['startDate'],
            "end_date" => $decorationItemInfo['endDate'],
            "quantity" => $decorationItemInfo['quantity'],
            "cost" => $decorationItemCost
        ]);
        $soundCost=ResourcesHelper::getCost('Sound', $soundInfo['id'], 1);
        $soundReservation = SoundReservation::create([
            "sound_id" => $soundInfo['id'],
            "event_id" => $soundInfo['eventId'],
            "start_date" => $soundInfo['startDate'],
            "end_date" => $soundInfo['endDate'],
            "cost" => $soundCost
        ]);
        $securityCost=$securityCost;
        $securityReservation = SecurityReservation::create([
            "security_id" => $securityInfo['id'],
            "event_id" => $securityInfo['eventId'],
            "start_date" => $securityInfo['startDate'],
            "end_date" => $securityInfo['endDate'],
            "quantity" => $securityInfo['quantity'],
            "cost" => $securityCost
        ]);
        $foodCost=ResourcesHelper::getCost('Food', $foodInfo['id'], $foodInfo['quantity']);
        $foodReservation = FoodReservation::create([
            "food_id" => $foodInfo['id'],
            "event_id" => $foodInfo['eventId'],
            "quantity" => $foodInfo['quantity'],
            "serving_date" => $foodInfo['servingDate'],
            "total_price" => $foodCost
        ]);
        $drinkCost=ResourcesHelper::getCost('Drink', $drinkInfo['id'], $drinkInfo['quantity']);
        $drinkReservation = DrinkReservation::create([
            "drink_id" => $drinkInfo['id'],
            "event_id" => $drinkInfo['eventId'],
            "quantity" => $drinkInfo['quantity'],
            "serving_date" => $drinkInfo['servingDate'],
            "total_price" => $drinkCost
        ]);
        $totalCost=$venueCost+$furnitureCost+$decorationItemCost+$soundCost+$securityCost+$foodCost+$drinkCost;

        $event=Event::where('id',$eventId)->first();
        $event->category_id->$request->input('Category')->id;
        $event('total_cost')->$totalCost;

        if($venueReservation&&$furnitureReservation&&$decorationItemReservation&&$soundReservation&&$securityReservation&&$foodReservation&&$drinkReservation)
            return response()->json(['message'=>__('event.completeStepTow')],201);
        return response()->json(['message'=>__('event.errorIncompleteStepTow')],400);
    }
}
