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
use DateTime;
use Illuminate\Support\Facades\DB;
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
        DB::beginTransaction();
        try {
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
            DB::commit();
            if ($event)
                return response()->json(['message' => __('event.completeStepOne'), 'eventId' => $event->id,], 201);
            return response()->json(['message' => __('event.errorIncompleteStepOne'), 'eventId' => $event->id,], 400);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => __('event.errorIncompleteStepOne'), 'error' => $e->getMessage()], 400);
        }
    }


    public function storeStep2(Request $request)
    {
        $totalCost = 0;
        $eventId = $request->input('Venue')[0]['eventId'];
        // RequestInfo
        $categoryInfo = $request->input('Category')[0];
        $venueInfo = $request->input('Venue')[0];
        $furnitureInfo = $request->input('Furniture', []);
        $decorationItemInfo = $request->input('DecorationItem', []);
        $soundInfo = $request->input('Sound', []);
        $securityInfo = $request->input('Security', []);
        $foodInfo = $request->input('Food', []);
        $drinkInfo = $request->input('Drink', []);


        $venueCost = ResourcesHelper::getCost('Venue', $venueInfo['id'], 1);
        $totalCost += $venueCost;
        $venueReservation = VenueReservation::create([
            "venue_id" => $venueInfo['id'],
            "event_id" => $venueInfo['eventId'],
            "start_date" => $venueInfo['startDate'],
            "end_date" => $venueInfo['endDate'],
            "cost" => $venueCost
        ]);


        foreach ($furnitureInfo as $furnitureItem) {
            $furnitureCost = ResourcesHelper::getCost('Furniture', $furnitureItem['id'], $furnitureItem['quantity']);
            $totalCost += $furnitureCost;
            FurnitureReservation::create([
                "furniture_id" => $furnitureItem['id'],
                "event_id" => $furnitureItem['eventId'],
                "start_date" => $furnitureItem['startDate'],
                "end_date" => $furnitureItem['endDate'],
                "quantity" => $furnitureItem['quantity'],
                "cost" => $furnitureCost
            ]);
        }


        foreach ($decorationItemInfo as $decorationItem) {
            $decorationItemCost = ResourcesHelper::getCost('DecorationItem', $decorationItem['id'], $decorationItem['quantity']);
            $totalCost += $decorationItemCost;
            DecorationItemReservation::create([
                "decoration_item_id" => $decorationItem['id'],
                "event_id" => $decorationItem['eventId'],
                "start_date" => $decorationItem['startDate'],
                "end_date" => $decorationItem['endDate'],
                "quantity" => $decorationItem['quantity'],
                "cost" => $decorationItemCost
            ]);
        }


        foreach ($soundInfo as $soundItem) {
            $soundCost = ResourcesHelper::getCost('Sound', $soundItem['id'], 1);
            $totalCost += $soundCost;
            SoundReservation::create([
                "sound_id" => $soundItem['id'],
                "event_id" => $soundItem['eventId'],
                "start_date" => $soundItem['startDate'],
                "end_date" => $soundItem['endDate'],
                "cost" => $soundCost
            ]);
        }


        foreach ($securityInfo as $securityItem) {
            $securityCost = ResourcesHelper::getCost('Security', $securityItem['id'], $securityItem['quantity']);
            $totalCost += $securityCost;
            SecurityReservation::create([
                "security_id" => $securityItem['id'],
                "event_id" => $securityItem['eventId'],
                "start_date" => $securityItem['startDate'],
                "end_date" => $securityItem['endDate'],
                "quantity" => $securityItem['quantity'],
                "cost" => $securityCost
            ]);
        }


        foreach ($foodInfo as $foodItem) {
            $foodCost = ResourcesHelper::getCost('Food', $foodItem['id'], $foodItem['quantity']);
            $totalCost += $foodCost;
            FoodReservation::create([
                "food_id" => $foodItem['id'],
                "event_id" => $foodItem['eventId'],
                "quantity" => $foodItem['quantity'],
                "serving_date" => $foodItem['servingDate'],
                "total_price" => $foodCost
            ]);
        }

        foreach ($drinkInfo as $drinkItem) {
            $drinkCost = ResourcesHelper::getCost('Drink', $drinkItem['id'], $drinkItem['quantity']);
            $totalCost += $drinkCost;
            DrinkReservation::create([
                "drink_id" => $drinkItem['id'],
                "event_id" => $drinkItem['eventId'],
                "quantity" => $drinkItem['quantity'],
                "serving_date" => $drinkItem['servingDate'],
                "total_price" => $drinkCost
            ]);
        }


        $event = Event::where('id', $eventId)->first();
        $event->category_id = $categoryInfo['id'];
        $event->total_cost = $totalCost;
        $event->save();
        if ($event)
            return response()->json(['message' => __('event.completeStepTow')], 201);

        return response()->json(['message' => __('event.errorIncompleteStepTow')], 400);
    }

    //public function storeStep2(Request $request)
//    {
//        $totalCost = 0;
//        $eventId = $request->input('Venue')->eventId;
//        //RequestInfo
//        $categoryInfo = $request->input('Category');
//        $venueInfo = $request->input('Venue');
//        $furnitureInfo = $request->input('Furniture');
//        $decorationItemInfo = $request->input('DecorationItem');
//        $soundInfo = $request->input('Sound');
//        $securityInfo = $request->input('Security');
//        $foodInfo = $request->input('Food');
//        $drinkInfo = $request->input('Drink');
//
//
//
//        $venueCost=ResourcesHelper::getCost('Venue', $venueInfo['id'], 1);
//        $venueReservation = VenueReservation::create([
//            "venue_id" => $venueInfo['id'],
//            "event_id" => $venueInfo['eventId'],
//            "start_date" => $venueInfo['startDate'],
//            "end_date" => $venueInfo['endDate'],
//            "cost" => $venueCost
//        ]);
//        $furnitureCost=ResourcesHelper::getCost('Furniture', $furnitureInfo['id'], $furnitureInfo['quantity']);
//        $furnitureReservation = FurnitureReservation::create([
//            "furniture_id" => $furnitureInfo['id'],
//            "event_id" => $furnitureInfo['eventId'],
//            "start_date" => $furnitureInfo['startDate'],
//            "end_date" => $furnitureInfo['endDate'],
//            "quantity" => $furnitureInfo['quantity'],
//            "cost" => $furnitureCost
//        ]);
//        $decorationItemCost=ResourcesHelper::getCost('DecorationItem', $decorationItemInfo['id'], $decorationItemInfo['quantity']);
//        $decorationItemReservation = DecorationItemReservation::create([
//            "decoration_item_id" => $decorationItemInfo['id'],
//            "event_id" => $decorationItemInfo['eventId'],
//            "start_date" => $decorationItemInfo['startDate'],
//            "end_date" => $decorationItemInfo['endDate'],
//            "quantity" => $decorationItemInfo['quantity'],
//            "cost" => $decorationItemCost
//        ]);
//        $soundCost=ResourcesHelper::getCost('Sound', $soundInfo['id'], 1);
//        $soundReservation = SoundReservation::create([
//            "sound_id" => $soundInfo['id'],
//            "event_id" => $soundInfo['eventId'],
//            "start_date" => $soundInfo['startDate'],
//            "end_date" => $soundInfo['endDate'],
//            "cost" => $soundCost
//        ]);
//        $securityCost=$securityCost;
//        $securityReservation = SecurityReservation::create([
//            "security_id" => $securityInfo['id'],
//            "event_id" => $securityInfo['eventId'],
//            "start_date" => $securityInfo['startDate'],
//            "end_date" => $securityInfo['endDate'],
//            "quantity" => $securityInfo['quantity'],
//            "cost" => $securityCost
//        ]);
//        $foodCost=ResourcesHelper::getCost('Food', $foodInfo['id'], $foodInfo['quantity']);
//        $foodReservation = FoodReservation::create([
//            "food_id" => $foodInfo['id'],
//            "event_id" => $foodInfo['eventId'],
//            "quantity" => $foodInfo['quantity'],
//            "serving_date" => $foodInfo['servingDate'],
//            "total_price" => $foodCost
//        ]);
//        $drinkCost=ResourcesHelper::getCost('Drink', $drinkInfo['id'], $drinkInfo['quantity']);
//        $drinkReservation = DrinkReservation::create([
//            "drink_id" => $drinkInfo['id'],
//            "event_id" => $drinkInfo['eventId'],
//            "quantity" => $drinkInfo['quantity'],
//            "serving_date" => $drinkInfo['servingDate'],
//            "total_price" => $drinkCost
//        ]);
//        $totalCost=$venueCost+$furnitureCost+$decorationItemCost+$soundCost+$securityCost+$foodCost+$drinkCost;
//
//        $event=Event::where('id',$eventId)->first();
//        $event->category_id->$request->input('Category')->id;
//        $event('total_cost')->$totalCost;
//
//        if($venueReservation&&$furnitureReservation&&$decorationItemReservation&&$soundReservation&&$securityReservation&&$foodReservation&&$drinkReservation)
//            return response()->json(['message'=>__('event.completeStepTow')],201);
//        return response()->json(['message'=>__('event.errorIncompleteStepTow')],400);
//    }
    public function remove(Request $request)
    {
        $eventId = $request->input('eventId');
        $currentDateTimeStr = $request->input('currentDateTime');
        $desire = $request->input('desire');

        // Convert currentDateTime to DateTime object
        $currentDateTime = new DateTime($currentDateTimeStr);

        $event = Event::find($eventId);

        // Check if event exists
        if (!$event) {
            return response()->json(['message' => __('event.notFound')], 404);
        }

        $eventStartDate = new DateTime($event->start_date);
        $dateDifference = $currentDateTime->diff($eventStartDate);

        if ($desire == 'delete') {
            if ($currentDateTime < $eventStartDate && $dateDifference->days <= 7) {
                return response()->json(['message' => __('event.deleteErrorBeforeEvent')], 400);
            } else {
                $event->delete();
                return response()->json(['message' => __('event.deleteSuccess')], 200);
            }
        } else {
            $event->delete();
            return response()->json(['message' => __('event.deleteSuccess')], 200);
        }
    }

}
