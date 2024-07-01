<?php

namespace App\Http\Controllers;

use App\Helpers\EventHelper;
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
use App\Models\Wallet;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use LanguageDetection\Language;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Helpers\TranslationHelper;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Helpers\QR_CodeHelper;
use Illuminate\Support\Facades\Cache;

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
            $data['id'] = $event->id;
            $data['Description_ar'] = $event->description_ar;
            $data['Description_en'] = $event->description_en;
            $modelName = 'Event';

            DB::commit();
            if ($event) {
                QR_CodeHelper::generateAndSaveQrCode($data, $modelName);
                return response()->json(['message' => __('event.completeStepOne'), 'eventId' => $event->id,], 201);
            }
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

        $ticketPrices = self::calculateTicketPrices($eventId, $totalCost);
        $event = Event::where('id', $eventId)->first();
        $event->category_id = $categoryInfo['id'];
        $event->total_cost = $totalCost;
        $event->ticket_price = $ticketPrices['regularTicketPrice'];
        $event->vip_ticket_price = $ticketPrices['vipTicketPrice'];
        $event->save();
        $ownerId = $event->user_id;
        if ($event) {
            $withdrawResult = WalletController::withdraw($ownerId, $totalCost);
            if ($withdrawResult['status'] === true)
                return response()->json(['message' => __('event.completeStepTow'), 'event' => $event], 201);
            return response()->json(['message' => __('event.errorIncompleteStepTow'), 'withdraw error' => $withdrawResult['message']], 201);
        }
        return response()->json(['message' => __('event.errorIncompleteStepTow')], 400);
    }

    public static function calculateTicketPrices($eventId, $totalCost)
    {
        $numberOfRegularChairs = ResourcesHelper::getNumberOfRegularChairReserved($eventId);
        $numberOfVipChairs = ResourcesHelper::getNumberOfVipChairReserved($eventId);

        $totalChairs = $numberOfRegularChairs + $numberOfVipChairs;

        // Calculate rates
        if ($totalChairs > 0) {
            $regularRate = $numberOfRegularChairs / $totalChairs;
            $vipRate = $numberOfVipChairs / $totalChairs;
        }

        // Calculate ticket prices
        $regularTicketPrice = 0;
        $vipTicketPrice = 0;
        // dd($numberOfVipChairs,$vipRate,$numberOfRegularChairs,$regularRate,$totalChairs,$totalCost);
        if ($numberOfVipChairs > 0 && $numberOfRegularChairs > 0) {
            $vipTicketPrice = ($totalCost * $vipRate) / $numberOfVipChairs;
            $regularTicketPrice = ($totalCost * $regularRate) / $numberOfRegularChairs;
            return ['regularTicketPrice' => $regularTicketPrice, 'vipTicketPrice' => $vipTicketPrice];
        } else if ($numberOfRegularChairs > 0 && $numberOfVipChairs <= 0) {
            $regularTicketPrice = ($totalCost * $regularRate) / $numberOfRegularChairs;
            return ['regularTicketPrice' => $regularTicketPrice, 'vipTicketPrice' => 0];
        } else if ($numberOfRegularChairs <= 0 && $numberOfVipChairs > 0) {
            $vipTicketPrice = ($totalCost * $vipRate) / $numberOfVipChairs;
            return ['regularTicketPrice' => 0, 'vipTicketPrice' => $vipTicketPrice];
        } else {
            // Get the venue capacity if no chairs are reserved
            $capacity = ResourcesHelper::getVenueCapacity($eventId);
            if ($capacity > 0) {
                return ['regularTicketPrice' => $totalCost / $capacity, 'vipTicketPrice' => 0];
            }
        }
        throw new Exception("Invalid venue capacity for event ID: $eventId");
    }

    public function remove(Request $request)
    {
        $eventId = $request->input('eventId');
        $event = Event::find($eventId);
        $ownerId = $event->user_id;
        $desire = $request->input('desire');
        $dates = EventHelper::getEventDates($eventId);
        if ($desire == 'delete') {
            if ($dates['currentDateTime'] < $dates['eventStartDate'] && $dates['creationToActionDiff']->h <= 1 && $dates['creationToActionDiff']->days == 0) {
                // Delete the event if it was created less than 2 hours ago
                WalletController::depositStatic(0.02, $ownerId, $event->total_cost);
                $event->delete();
                return response()->json(['message' => __('event.deleteSuccess')], 200);
            }
            if ($dates['currentDateTime'] < $dates['eventStartDate'] && $dates['dateDifference']->days <= 7) {
                return response()->json(['message' => __('event.deleteErrorBeforeEvent')], 400);
            } else {
                WalletController::depositStatic(0.02, $ownerId, $event->total_cost);
                $event->delete();
                return response()->json(['message' => __('event.deleteSuccess')], 200);
            }
        } else if ($desire === 'cancel') { // for canceling the event creation
            $event->delete();
            return response()->json(['message' => __('event.deleteSuccess')], 200);
        }
    }

    public function getPrices(Request $request)
    {
        $eventId = $request->input('eventId');
        $event = Event::find($eventId);
        $regularTicketPrice = $event->ticket_price;
        $vipTicketPrice = $event->vip_ticket_price;
        $totalCost = $event->total_cost;
        return response()->json([
            'totalCost' => $totalCost,
            'regularTicketPrice' => $regularTicketPrice,
            'vipTicketPrice' => $vipTicketPrice], 200);
    }

    public function adjustPrices(Request $request)
    {

        $newRegularTicketPrice = $request->input('newRegularTicketPrice');
        $newVipTicketPrice = $request->input('newVipTicketPrice');
        $eventId = $request->input('eventId');
        $event = Event::find($eventId);
        $dates = EventHelper::getEventDates($eventId);
        if ($dates['currentDateTime'] < $dates['eventStartDate'] && $dates['dateDifference']->days <= 7) {
            return response()->json(['message' => __('event.adjustPricesErrorBeforeEvent')], 400);
        } else {
            if ($event->ticket_price > 0 && $event->vip_ticket_price > 0) {
                $event->ticket_price = $newRegularTicketPrice;
                $event->vip_ticket_price = $newVipTicketPrice;
                $event->save();
                return response()->json(['message' => __('event.priceAdjustSuccess')], 200);
            } else
                if ($event->ticket_price > 0 && $event->vip_ticket_price <= 0) {
                    $event->ticket_price = $newRegularTicketPrice;
                    $event->save();
                    return response()->json(['message' => __('event.priceAdjustSuccess')], 200);
                } else if ($event->ticket_price <= 0 && $event->vip_ticket_price > 0) {
                    $event->vip_ticket_price = $newVipTicketPrice;
                    $event->save();
                    return response()->json(['message' => __('event.priceAdjustSuccess')], 200);
                }

        }
    }

    public function getEventReservations(Request $request)
    {
        $eventId = $request->input('eventId');
        $event = Event::with([
            'furnitureReservations',
            'soundReservations',
            'securityReservations',
            'foodReservations',
            'drinkReservations',
            'decorationItemReservations'
        ])->find($eventId);

        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        return response()->json([
            'furniture_reservations' => $event->furnitureReservations,
            'music_reservations' => $event->soundReservations,
            'security_reservations' => $event->securityReservations,
            'food_reservations' => $event->foodReservations,
            'drink_reservations' => $event->drinkReservations,
            'decoration_item_reservations' => $event->decorationItemReservations
        ]);

    }

    public function updateEventQuantitiesReservations(Request $request)// furniture, security,decoration items
    {
        $newItems = $request->input('newItems');
        $type = $request->input('itemsType');
        $eventId = $request->input('eventId');
        $ownerId = $request->input('userId');

        // Initialize model variables based on item type
        $modelName = '';
        $modelReservations = '';
        $modelReservation = '';
        $modelNameId = '';
        $costAttribute = 'cost';
        switch ($type) {
            case 'furniture':
                $modelName = 'Furniture';
                $modelReservations = 'FurnitureReservations';
                $modelReservation = 'FurnitureReservation';
                $modelNameId = 'furniture_id';

                break;
            case 'decorationItem':
                $modelName = 'DecorationItem';
                $modelReservations = 'DecorationItemReservations';
                $modelReservation = 'DecorationItemReservation';
                $modelNameId = 'decoration_item_id';
                break;
            case 'security':
                $modelName = 'Security';
                $modelReservations = 'SecurityReservations';
                $modelReservation = 'SecurityReservation';
                $modelNameId = 'security_id';

                break;
            case 'food':
                $modelName = 'Food';
                $modelReservations = 'FoodReservations';
                $modelReservation = 'FoodReservation';
                $modelNameId = 'food_id';
                $costAttribute = 'total_price';
                break;
            case 'drink':
                $modelName = 'Drink';
                $modelReservations = 'DrinkReservations';
                $modelReservation = 'DrinkReservation';
                $modelNameId = 'drink_id';
                $costAttribute = 'total_price';
                break;
            default:
                return response()->json(['message' => __('event.invalidItemType')], 400);

        }
        $event = Event::find($eventId);
        $dates = EventHelper::getEventDates($eventId);
        $updateCost = 0;
        // Check if the event is within 7 days
        if ($dates['dateDifference']->days <= 7) {
            return response()->json(['message' => __('event.updateReservationsErrorDate')], 400);
        }
        // Step 1: Create a dictionary to map objects_id to the reservation
        $reservationMap = [];
        foreach ($event->$modelReservations as $reservation) {
            $reservationMap[$reservation->$modelNameId] = $reservation;
        }

// Step 2: Iterate through $newItems and use the dictionary for quick lookup
        foreach ($newItems as $newItem) {
            $found = false;
            if (isset($reservationMap[$newItem['id']])) { //found we should update it
                // Adjust the quantity if already reserved
                $reservation = $reservationMap[$newItem['id']];
                $oldQuantity = $reservation->quantity;
                $oldCost = $reservation->$costAttribute;
                $newQuantity = $newItem['newQuantity'];
                if ($newQuantity === 0) {
                    $modelClass = "App\\Models\\$modelReservation";
                    $instance = $modelClass::find($reservationMap[$newItem['id']]->id);
                    $reservationCost = $instance->$costAttribute;
                    $instance->delete();
                    WalletController::depositStatic(0.02, $ownerId, $reservationCost);
                    $updateCost -= $reservationCost;
                    $found = true;
                } else {
                    $newCost = ResourcesHelper::getCost($modelName, $newItem['id'], $newQuantity);
                    $costDifference = $newCost - $oldCost;
                    if ($costDifference <= 0) { // lessQuantity
                        if ($costDifference < 0)
                            WalletController::depositStatic(0.02, $ownerId, (-1 * $costDifference));
                    } else {  // cost difference >0
                        $withdrawResult = WalletController::withdraw($ownerId, $costDifference);
                        if ($withdrawResult['status'] === false)
                            return response()->json(['withdraw error' => $withdrawResult['message']], 400);
                    }

                    $reservation->$costAttribute = $newCost;
                    $reservation->quantity = $newQuantity;
                    if($type === 'furniture'  ||$type === 'security'  ||$type === 'decorationItem'   ) {
                        $reservation->start_date = $newItem['newStartDate'];
                        $reservation->end_date = $newItem['newEndDate'];
                    }else{
                        $reservation->serving_date=$newItem['servingDate'];
                    }
                    $reservation->save();
                    $updateCost -= $oldCost;
                    $updateCost += $newCost;
                    $found = true;

                }
            }


            if (!$found) { // Create a new reservation if not found
                $newQuantity = $newItem['newQuantity'];
                $cost = ResourcesHelper::getCost($modelName, $newItem['id'], $newQuantity);
                $withdrawResult = WalletController::withdraw($ownerId, $cost);
                if ($withdrawResult['status'] === false)
                    return response()->json(['withdraw error' => $withdrawResult['message']], 400);

                $reservationData = [
                    'event_id' => $eventId,
                    $modelNameId => $newItem['id'],
                    'quantity' => $newQuantity,
                    $costAttribute => $cost,
                ];
                if ($type === 'furniture' || $type === 'security' || $type === 'decorationItem') {
                    $reservationData['start_date'] = $newItem['newStartDate'];
                    $reservationData['end_date'] = $newItem['newEndDate'];
                } else {
                    $reservationData['serving_date'] = $newItem['servingDate'];
                }

                "App\\Models\\$modelReservation"::create($reservationData);
                $updateCost += $cost;
            }
        }
        // Update the event's total cost
        $event->total_cost += $updateCost;
        $totalCost = $event->total_cost;
        $ticketPrices = self::calculateTicketPrices($eventId, $totalCost);
        $event->ticket_price = $ticketPrices['regularTicketPrice'];
        $event->vip_ticket_price = $ticketPrices['vipTicketPrice'];
        $event->save();
        return response()->json(['message' => __('event.updateReservationsSuccess')]);
    }


    public function updateEventSoundAndVenueReservations(Request $request)
    {  //sound venue
        $newItems = $request->input('newItems');
        $type = $request->input('itemsType');
        $eventId = $request->input('eventId');
        $ownerId = $request->input('userId');

        // Initialize model variables based on item type
        $modelName = '';
        $modelReservations = '';
        $modelReservation = '';
        $modelNameId = '';

        switch ($type) {
            case 'sound':
                $modelName = 'Sound';
                $modelReservations = 'SoundReservations';
                $modelReservation = 'SoundReservation';
                $modelNameId = 'sound_id';
                break;
            case 'venue':
                $modelName = 'Venue';
                $modelReservations = 'VenueReservations';
                $modelReservation = 'VenueReservation';
                $modelNameId = 'venue_id';
                break;
        }
        $event = Event::find($eventId);
        $dates = EventHelper::getEventDates($eventId);
        $updateCost = 0;
        // Check if the event is within 7 days
        if ($dates['dateDifference']->days <= 7) {
            return response()->json(['message' => __('event.updateReservationsErrorDate')], 400);
        }
        // Step 1: Create a dictionary to map objects_id to the reservation
        $reservationMap = [];
        foreach ($event->$modelReservations as $reservation) {
            $reservationMap[$reservation->$modelNameId] = $reservation;
        }
        foreach ($newItems as $newItem) {
            $found = false;
            if (isset($reservationMap[$newItem['id']])) {// found we will delete it
                $modelClass = "App\\Models\\$modelReservation";
                $instance = $modelClass::find($reservationMap[$newItem['id']]->id);
                $reservationCost = $instance->cost;
                $instance->delete();
                WalletController::depositStatic(0.02, $ownerId, $reservationCost);
                $updateCost -= $reservationCost;
                $found = true;
            }
            if (!$found)// not found we will create a new reservation for it
            {
                $cost = ResourcesHelper::getCost($modelName, $newItem['id'], 1);
                $withdrawResult = WalletController::withdraw($ownerId, $cost);
                if ($withdrawResult['status'] === false) //user cannot pay
                    return response()->json(['withdraw error' => $withdrawResult['message']], 400);

                "App\\Models\\$modelReservation"::create([
                'event_id' => $eventId,
                $modelNameId => $newItem['id'],
                'start_date' => $newItem['newStartDate'],
                'end_date' => $newItem['newEndDate'],
                'cost' => $cost,
            ]);

                $updateCost += $cost;
        }
        }
        // Update the event's total cost
        $event->total_cost += $updateCost;
        $totalCost = $event->total_cost;
        $ticketPrices = self::calculateTicketPrices($eventId, $totalCost);
        $event->ticket_price = $ticketPrices['regularTicketPrice'];
        $event->vip_ticket_price = $ticketPrices['vipTicketPrice'];
        $event->save();
        return response()->json(['message' => __('event.updateReservationsSuccess')]);
    }

    public function updateEventCateringReservations(Request $request)
    {

    }

}
