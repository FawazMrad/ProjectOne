<?php

namespace App\Http\Controllers;

use App\Helpers\DateTimeHelper;
use App\Helpers\EventHelper;
use App\Helpers\ResourcesHelper;
use App\Models\Category;
use App\Models\DecorationItemReservation;
use App\Models\DrinkReservation;
use App\Models\FoodReservation;
use App\Models\Friendship;
use App\Models\FurnitureReservation;
use App\Models\Security;
use App\Models\SecurityReservation;
use App\Models\SoundReservation;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueReservation;
use App\Models\Wallet;
use DateTime;
use Exception;
use http\Env\Response;
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
        $user = $request->user();
        $userId = $user->id;

        $validatedData = $request->validate(['categoryId' => 'required|exists:categories,id', 'title' => 'required|string|max:100', 'description' => 'required|string', 'minAge' => 'required|integer|min:0', 'isPaid' => 'required|boolean', 'isPrivate' => 'required|boolean', 'attendanceType' => 'required|in:INVITATION,TICKET', 'image' => 'nullable|string', 'startDate' => 'required|date', 'endDate' => 'required|date|after_or_equal:startDate',]);

        // Translate the description
        try {
            $validatedData = TranslationHelper::descriptionAndTranslatedDescription($validatedData);
        } catch (Exception $e) {
            return response()->json(['message' => __('event.errorIncompleteStepOne'), 'error' => $e->getMessage()], 400);
        }

        DB::beginTransaction();
        try {
            // Create the event
            $event = Event::create(['user_id' => $userId, 'category_id' => $validatedData['categoryId'], 'title' => $validatedData['title'], 'description_ar' => $validatedData['description_ar'], 'description_en' => $validatedData['description_en'], 'min_age' => $validatedData['minAge'], 'is_paid' => $validatedData['isPaid'], 'is_private' => $validatedData['isPrivate'], 'attendance_type' => $validatedData['attendanceType'], 'image' => $validatedData['image'], 'start_date' => $validatedData['startDate'], 'end_date' => $validatedData['endDate'],]);

            $data['id'] = $event->id;
            $data['Description_ar'] = $event->description_ar;
            $data['Description_en'] = $event->description_en;
            $modelName = 'Event';

            DB::commit();
            if ($event) {
                EventHelper::changeUserRating($user, 0.2);
                QR_CodeHelper::generateAndSaveQrCode($data, $modelName);
                return response()->json(['message' => __('event.completeStepOne'), 'event' => $event], 201);
            }

            return response()->json(['message' => __('event.errorIncompleteStepOne'), 'event' => $event->id,], 400);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => __('event.errorIncompleteStepOne'), 'error' => $e->getMessage()], 400);
        }
    }

    public function storeStep2(Request $request)
    {
        $totalCost = 0;
        $eventId = $request->input('eventId');
        $event = Event::find($eventId);
        $dates = EventHelper::getEventDates($eventId);
        // RequestInfo
        $venueInfo = $request->input('Venue')[0];
        $furnitureInfo = $request->input('Furniture', []);
        $decorationItemInfo = $request->input('DecorationItem', []);
        $soundInfo = $request->input('Sound', []);
        $securityInfo = $request->input('Security', []);
        $foodInfo = $request->input('Food', []);
        $drinkInfo = $request->input('Drink', []);


        $venueCost = ResourcesHelper::getCost('Venue', $venueInfo['id'], 1);
        $totalCost += $venueCost;
        $venueReservation = VenueReservation::create(["venue_id" => $venueInfo['id'], "event_id" => $eventId, "start_date" => $dates['startDate'], "end_date" => $dates['endDate'], "cost" => $venueCost]);


        foreach ($furnitureInfo as $furnitureItem) {
            $furnitureCost = ResourcesHelper::getCost('Furniture', $furnitureItem['id'], $furnitureItem['quantity']);
            $totalCost += $furnitureCost;
            FurnitureReservation::create(["furniture_id" => $furnitureItem['id'], "event_id" => $eventId, "start_date" => $dates['startDate'], "end_date" => $dates['endDate'], "quantity" => $furnitureItem['quantity'], "cost" => $furnitureCost]);
        }


        foreach ($decorationItemInfo as $decorationItem) {
            $decorationItemCost = ResourcesHelper::getCost('DecorationItem', $decorationItem['id'], $decorationItem['quantity']);
            $totalCost += $decorationItemCost;
            DecorationItemReservation::create(["decoration_item_id" => $decorationItem['id'], "event_id" => $eventId, "start_date" => $decorationItem['startDate'], "end_date" => $dates['endDate'], "quantity" => $decorationItem['quantity'], "cost" => $decorationItemCost]);
        }


        foreach ($soundInfo as $soundItem) {
            $soundCost = ResourcesHelper::getCost('Sound', $soundItem['id'], 1);
            $totalCost += $soundCost;
            SoundReservation::create(["sound_id" => $soundItem['id'], "event_id" => $eventId, "start_date" => $soundItem['startDate'], "end_date" => $soundItem['endDate'], "cost" => $soundCost]);
        }


        foreach ($securityInfo as $securityItem) {
            $securityCost = ResourcesHelper::getCost('Security', $securityItem['id'], $securityItem['quantity']);
            $totalCost += $securityCost;
            SecurityReservation::create(["security_id" => $securityItem['id'], "event_id" => $eventId, "start_date" => $dates['startDate'], "end_date" => $dates['endDate'], "quantity" => $securityItem['quantity'], "cost" => $securityCost]);
        }


        foreach ($foodInfo as $foodItem) {
            $foodCost = ResourcesHelper::getCost('Food', $foodItem['id'], $foodItem['quantity']);
            $totalCost += $foodCost;
            FoodReservation::create(["food_id" => $foodItem['id'], "event_id" => $eventId, "quantity" => $foodItem['quantity'], "serving_date" => $foodItem['servingDate'], "total_price" => $foodCost]);
        }

        foreach ($drinkInfo as $drinkItem) {
            $drinkCost = ResourcesHelper::getCost('Drink', $drinkItem['id'], $drinkItem['quantity']);
            $totalCost += $drinkCost;
            DrinkReservation::create(["drink_id" => $drinkItem['id'], "event_id" => $eventId, "quantity" => $drinkItem['quantity'], "serving_date" => $drinkItem['servingDate'], "total_price" => $drinkCost]);
        }
        if ($event->is_paid) {
            $ticketPrices = self::calculateTicketPrices($eventId, $totalCost);
            $event->ticket_price = $ticketPrices['regularTicketPrice'];
            $event->vip_ticket_price = $ticketPrices['vipTicketPrice'];
        } else {
            $event->ticket_price = 0;
            $event->vip_ticket_price = 0;
        }
        $event->total_cost = $totalCost;

        $event->save();
        $ownerId = $event->user_id;
        if ($event) {
            $withdrawResult = WalletController::withdraw($ownerId, $totalCost);
            if ($withdrawResult['status'] === true) return response()->json(['message' => __('event.completeStepTow'), 'event' => $event], 201);
            return response()->json(['message' => __('event.errorIncompleteStepTow'), 'withdraw error' => $withdrawResult['message']], 201);
        }
        return response()->json(['message' => __('event.errorIncompleteStepTow')], 400);
    }

    public static function calculateTicketPrices($eventId, $totalCost)
    {
        $numberOfRegularChairs = ResourcesHelper::getNumberOfRegularChairReserved($eventId);
        $numberOfVipChairs = ResourcesHelper::getNumberOfVipChairReserved($eventId);

        $totalChairs = $numberOfRegularChairs + $numberOfVipChairs;

        if ($totalChairs == 0) {
            $capacity = ResourcesHelper::getVenueCapacity($eventId);
            if ($capacity > 0) {
                return ['regularTicketPrice' => $totalCost / $capacity, 'vipTicketPrice' => 0];
            } else {
                throw new Exception("Invalid venue capacity for event ID: $eventId");
            }
        }

        $regularRate = $numberOfRegularChairs / $totalChairs;
        $vipRate = $numberOfVipChairs / $totalChairs;

        $regularTicketPrice = $numberOfRegularChairs > 0 ? ($totalCost * $regularRate) / $numberOfRegularChairs : 0;
        $vipTicketPrice = $numberOfVipChairs > 0 ? ($totalCost * $vipRate) / $numberOfVipChairs : 0;

        return ['regularTicketPrice' => $regularTicketPrice, 'vipTicketPrice' => $vipTicketPrice,];
    }

    public function remove(Request $request)
    {
        $user = $request->user();
        $eventId = $request->input('eventId');
        $event = Event::find($eventId);
        $ownerId = $event->user_id;
        $desire = $request->input('desire');
        $dates = EventHelper::getEventDates($eventId);
        if ($desire == 'delete') {
            if ($dates['currentDateTime'] < $dates['startDate'] && $dates['creationToActionDiff']->h <= 1 && $dates['creationToActionDiff']->days == 0) {
                // Delete the event if it was created less than 2 hours ago
                WalletController::depositStatic(0.02, $ownerId, $event->total_cost);
                $event->delete();
                EventHelper::changeUserRating($user, -0.3);
                return response()->json(['message' => __('event.deleteSuccess')], 200);
            }
            if ($dates['currentDateTime'] < $dates['startDate'] && $dates['dateDifference']->days <= 7) {
                return response()->json(['message' => __('event.deleteErrorBeforeEvent')], 400);
            } else {
                WalletController::depositStatic(0.02, $ownerId, $event->total_cost);
                $event->delete();
                EventHelper::changeUserRating($user, -0.3);
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
        if ($event->is_paid) {
            $regularTicketPrice = $event->ticket_price;
            $vipTicketPrice = $event->vip_ticket_price;
            $totalCost = $event->total_cost;
            return response()->json(['totalCost' => $totalCost, 'regularTicketPrice' => $regularTicketPrice, 'vipTicketPrice' => $vipTicketPrice], 200);
        }
        return response()->json(['message' => __('event.notPaid')], 400);
    }

    public function adjustPrices(Request $request)
    {
        $newRegularTicketPrice = $request->input('newRegularTicketPrice');
        $newVipTicketPrice = $request->input('newVipTicketPrice');
        $eventId = $request->input('eventId');
        $event = Event::find($eventId);
        $dates = EventHelper::getEventDates($eventId);
        if ($dates['currentDateTime'] < $dates['startDate'] && $dates['dateDifference']->days <= 5) {

            return response()->json(['message' => __('event.adjustPricesErrorBeforeEvent')], 400);
        } else {
            if ($event->ticket_price > 0 && $event->vip_ticket_price > 0) {
                $event->ticket_price = $newRegularTicketPrice;
                $event->vip_ticket_price = $newVipTicketPrice;
                $event->save();
                return response()->json(['message' => __('event.priceAdjustSuccess')], 200);
            } else if ($event->ticket_price > 0 && $event->vip_ticket_price <= 0) {
                $event->ticket_price = $newRegularTicketPrice;
                $event->save();
                return response()->json(['message' => __('event.priceAdjustSuccess')], 200);
            } else if ($event->ticket_price <= 0 && $event->vip_ticket_price > 0) {
                $event->vip_ticket_price = $newVipTicketPrice;
                $event->save();
                return response()->json(['message' => __('event.priceAdjustSuccess')], 200);
            }
            return response()->json(['message' => __('event.notPaid')], 400);
        }
    }

    public function getEventReservations(Request $request)
    {
        $eventId = $request->input('eventId');
        $event = Event::with(['furnitureReservations', 'soundReservations', 'securityReservations', 'foodReservations', 'drinkReservations', 'decorationItemReservations'])->find($eventId);

        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        return response()->json(['furniture_reservations' => $event->furnitureReservations, 'music_reservations' => $event->soundReservations, 'security_reservations' => $event->securityReservations, 'food_reservations' => $event->foodReservations, 'drink_reservations' => $event->drinkReservations, 'decoration_item_reservations' => $event->decorationItemReservations]);

    }

    public function updateEventQuantitiesReservations(Request $request)// furniture, security,decoration items
    {
        $newItems = $request->input('newItems');
        $type = $request->input('itemsType');
        $eventId = $request->input('eventId');
        $ownerId = $request->user()->id;

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
        if ($dates['dateDifference']->days <= 5) {
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
                        if ($costDifference < 0) WalletController::depositStatic(0.02, $ownerId, (-1 * $costDifference));
                    } else {  // cost difference >0
                        $withdrawResult = WalletController::withdraw($ownerId, $costDifference);
                        if ($withdrawResult['status'] === false) return response()->json(['withdraw error' => $withdrawResult['message']], 400);
                    }

                    $reservation->$costAttribute = $newCost;
                    $reservation->quantity = $newQuantity;
                    if ($type === 'decorationItem') {
                        $reservation->start_date = $newItem['newStartDate'];
                        $reservation->end_date = $dates['endDate'];
                    } else if ($type === 'food' || $type === 'drink') {
                        $reservation->serving_date = $newItem['servingDate'];
                    } else { //furniture ,security,
                        $reservation->start_date = $dates['startDate'];
                        $reservation->end_date = $dates['endDate'];
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
                if ($withdrawResult['status'] === false) return response()->json(['withdraw error' => $withdrawResult['message']], 400);

                $reservationData = ['event_id' => $eventId, $modelNameId => $newItem['id'], 'quantity' => $newQuantity, $costAttribute => $cost,];
                if ($type === 'decorationItem') {
                    $reservationData['start_date'] = $newItem['newStartDate'];
                    $reservationData['end_date'] = $dates['endDate'];
                } else if ($type === 'food' || $type === 'drink') {
                    $reservationData['serving_date'] = $newItem['servingDate'];
                } else { //furniture ,security,
                    $reservationData['start_date'] = $dates['startDate'];
                    $reservationData['end_date'] = $dates['endDate'];
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
        if ($dates['dateDifference']->days <= 5) {
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
                if ($withdrawResult['status'] === false) //user cannot pay return response()->json(['withdraw error' => $withdrawResult['message']], 400);
                    $reservationData = ['eventId' => $eventId, $modelNameId => $newItem['id'], 'startDate' => $newItem['newStartDate'], 'endDate' => $newItem['newEndDate'], 'cost' => $cost,];
                if ($type === 'venue') {
                    $reservationData = ['start_date' => $dates['startDate'], 'end_date' => $dates['endDate']];
                }
                "App\\Models\\$modelReservation"::create(['event_id' => $reservationData['eventId'], $modelNameId => $reservationData[$modelNameId], 'start_date' => $reservationData['startDate'], 'end_date' => $reservationData['endDate'], 'cost' => $reservationData['cost'],]);

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

    public function getEventsByCategory(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;

        $categoryName = $request->input('categoryName');
        $category = Category::where('name', $categoryName)->first();
        if (!$category) {
            return response()->json(['events' => __('event.noSuchCategory')], 404);
        }
        $categoryId = $category->id;

        $events = Event::where('category_id', $categoryId)->where('is_private', false)->where('start_date', '>', DateTimeHelper::getCurrentDateTime())->get();

        if ($events->isNotEmpty()) {
            $filteredEvents = EventHelper::filterEventsByBlockedFriendships($events, $user);
            if ($filteredEvents) {
                $filteredEvents = $filteredEvents->map(function ($filteredEvent) use ($userId) {
                    return EventHelper::putFavouriteStatusInEvent($filteredEvent, $userId);
                });
                return response()->json(['events' => $filteredEvents->values()], 200);
            }
            return response()->json(['events' => __('event.noSuchEvents')], 404);
        }

        return response()->json(['events' => __('event.noSuchEvents')], 404);
    }

    public function searchEvents(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;
        $query = Event::query();
        $query->where('is_private', false);

        if ($request->has('title')) {
            $title = $request->input('title');
            $query->where('title', 'LIKE', '%' . $title . '%');
        }
        if ($request->has('categoryName')) {
            $categoryName = $request->input('categoryName');
            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('name', 'LIKE', '%' . $categoryName . '%');
            });
        }

        if ($request->has('startDate') && $request->has('endDate')) {
            $query->whereBetween('start_date', [$request->input('startDate'), $request->input('endDate')]);
        }

        if ($request->has('location')) {
            $query->whereHas('venueReservation.venue', function ($q) use ($request) {
                $q->where('location', 'LIKE', '%' . $request->input('location') . '%');
            });
        }

        if ($request->has('minAge')) {
            $query->where('min_age', '>=', $request->input('minAge'));
        }
        if ($request->has('isFree')) {
            if ($request->input('isFree')) $isPaid = false; else
                $isPaid = true;
            $query->where('is_paid', $isPaid);
        }

        if ($request->has('priceRange')) {
            $priceRange = explode('-', $request->input('priceRange'));
            $vipPriceRange = explode('-', $request->input('vipPriceRange'));
            if (count($priceRange) == 2) {
                $query->whereBetween('ticket_price', [(float)$priceRange[0], (float)$priceRange[1]]);
            }
            if (count($vipPriceRange) == 2) {
                $query->whereBetween('vip_ticket_price', [(float)$vipPriceRange[0], (float)$vipPriceRange[1]]);
            }
        }

        $events = $query->get();

        if ($events->isNotEmpty()) {

            $filteredEvents = EventHelper::filterEventsByBlockedFriendships($events, $user);

            if ($filteredEvents) {
                return response()->json(['events' => $filteredEvents->values()], 200);
            }
            return response()->json(['events' => __('event.noSuchEvents')], 404);
        }
        return response()->json(['events' => __('event.noSuchEvents')], 404);
    }

    public function searchEventsByQR(Request $request)
    {
        $user = $request->user();
        $eventId = $request->input('eventId');
        $event = Event::find($eventId);
        $filteredEvent = EventHelper::filterEventsByBlockedFriendships($event, $user);
        if (count($filteredEvent) > 0) return response()->json(['event' => $filteredEvent], 200);
        return response()->json(['message' => __('event.noSuchEvents')], 404);
    }

    public function mostPopularEvents(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;
        $events = Event::select('events.*', 'users.rating as creator_rating')
            ->join('users', 'events.user_id', '=', 'users.id')
            ->where('is_private', false)
            ->orderBy('users.rating', 'desc')
            ->take(10)->get();

        if ($events) {
            $filteredEvents = EventHelper::filterEventsByBlockedFriendships($events, $user);

            if ($filteredEvents) {
                $filteredEvents = $filteredEvents->map(function ($filteredEvent) use ($userId) {
                    return EventHelper::putFavouriteStatusInEvent($filteredEvent, $userId);
                });
                return response()->json(['events' => $filteredEvents->values()], 200);
            }
            return response()->json(['events' => __('event.noPopularEvents')], 404);
        }
        return response()->json(['events' => __('event.noPopularEvents')], 404);
    }

    public function getEvent(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;
        $eventId = $request->input('eventId');
        $event = Event::find($eventId);

        if ($event) {
            $eventCreator = $event->user()->first();
            $friendship = Friendship::where(function ($query) use ($user, $eventCreator) {
                $query->where('sender_id', $user->id)->where('receiver_id', $eventCreator->id);
            })->orWhere(function ($query) use ($user, $eventCreator) {
                $query->where('sender_id', $eventCreator->id)->where('receiver_id', $user->id);
            })->first();

            if ($friendship && $friendship->status === 'BLOCKED') {
                return response()->json(['message' => __('event.eventNotFound')], 404);
            }
            $event = EventHelper::putFavouriteStatusInEvent($event, $userId);
            return response()->json(['event' => $event], 200);
        }

        return response()->json(['message' => __('event.eventNotFound')], 404);
    }

    public function calender(Request $request)
    {
        $user = $request->user();
        $createdEvents = EventHelper::getCreatedEventsCalender($user);
        $invitedEvents = EventHelper::getInvitedOrPurchasedEventsCalender($user, 'invited');
        $purchasedEvents = EventHelper::getInvitedOrPurchasedEventsCalender($user, 'purchased');

        $createdEventsCollection = collect($createdEvents);
        $invitedEventsCollection = collect($invitedEvents);
        $purchasedEventsCollection = collect($purchasedEvents);

        $allEvents = $createdEventsCollection->merge($invitedEventsCollection)->merge($purchasedEventsCollection);

        $uniqueEvents = $allEvents->unique('id');
        if ($uniqueEvents->isNotEmpty()) return response()->json(['Events' => $uniqueEvents], 200);
        return response()->json(['Events' => __('event.noSuchEvents')], 404);
    }
}
