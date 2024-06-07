<?php

namespace App\Http\Controllers;

use App\Helpers\ResourcesHelper;

use App\Models\Category;
use App\Models\DecorationItem;
use App\Models\DecorationItemReservation;
use App\Models\Event;
use App\Models\Food;
use App\Models\Drink;
use App\Models\Furniture;
use App\Models\FurnitureReservation;
use App\Models\Security;
use App\Models\SecurityReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ResourceController extends Controller
{
    public function getAvailableResources(Request $request)//Sound,Venue
    {
        $resource = ucfirst($request->input('resourceName'));
        $resourceSmallLetter = strtolower($request->input('resourceName'));
        $eventId = $request->input('eventId');
        // Check if the resource class exists
        if (!class_exists("App\\Models\\$resource")) {
            return response()->json(['error' => __('resource.invalidType')], 400);
        }

        $startAndEndDate = ResourcesHelper::getStartAndEndDateForEvent($eventId);


        if (empty($startAndEndDate['startDate']) || empty($startAndEndDate['endDate'])) {

            return response()->json(['error' => __('event.IdNotFoundOrDates')], 400);
        }

        $availableResources = "App\\Models\\$resource"::whereNotIn('id', function ($query) use ($resourceSmallLetter, $startAndEndDate) {
        $query->select("{$resourceSmallLetter}_id")->from("{$resourceSmallLetter}_reservations")
            ->where(function ($query) use ($startAndEndDate) {
                $query->whereBetween('start_date', [$startAndEndDate['startDate'], $startAndEndDate['endDate']])
                    ->orWhereBetween('end_date', [$startAndEndDate['startDate'], $startAndEndDate['endDate']])
                    ->orWhere(function ($query) use ($startAndEndDate) {
                        $query->where('start_date', '<=', $startAndEndDate['startDate'])->where('end_date', '>=', $startAndEndDate['endDate']);
                    });
            });
    })->get();

        return response()->json(['Available' . $resource . 's' => $availableResources], 200);
    }


    public function getAvailableResourcesWithQuantity(Request $request)//furniture,decorationItem,security
    {
        $resourceName = $request->input('resourceName');
        $resource = ucfirst($resourceName);
        $resourceSmallLetter = strtolower($resourceName);

        if ($resourceName === 'decoration_item' || $resourceName === 'decorationItem') {
            $resource = 'DecorationItem';
            $resourceSmallLetter = 'decoration_item';
        }

        $eventId = $request->input('eventId');
        $event = Event::find($eventId);

        if (!$event) {
            return response()->json(['error' => __('event.IdNotFoundOrDates')], 404);
        }

        $startAndEndDate = ResourcesHelper::getStartAndEndDateForEvent($eventId);

        $availableResources = [];
        $resourceItems = "App\\Models\\$resource"::all();
        foreach ($resourceItems as $item) {
            $reservationModel = "App\\Models\\{$resource}Reservation";
            $reservedQuantity = $reservationModel::where($resourceSmallLetter . '_id', $item->id)
                ->where(function ($query) use ($startAndEndDate) {
                    $query->whereBetween('start_date', [$startAndEndDate['startDate'], $startAndEndDate['endDate']])
                        ->orWhereBetween('end_date', [$startAndEndDate['startDate'], $startAndEndDate['endDate']])
                        ->orWhere(function ($query) use ($startAndEndDate) {
                            $query->where('start_date', '<=', $startAndEndDate['startDate'])
                                ->where('end_date', '>=', $startAndEndDate['endDate']);
                        });
                })
                ->sum('quantity');

            // Subtract reserved quantity from total quantity
            $availableQuantity = $item->quantity - $reservedQuantity;
            $availableResources[$resourceSmallLetter . '_items'][] = [
                'item' => $item,
                'availableQuantity' => $availableQuantity,
            ];
        }

        return response()->json($availableResources, 200);
    }

    public function getAvailableCatering(Request $request)
    { //Food and Drink
        $typeSmallLetter = $request->header('type');
        $type = ucfirst($request->header('type'));
        $getType = "App\\Models\\$type"::all();
    if (!$getType)
        return response()->json(['message' => __('resource.cateringNotFound')], 404);
    return response()->json([$type . ' available' => $getType], 200);

    }

    public function getCategories()
    {//Categories
        $catergories = Category::all();
        if (!$catergories)
            return response()->json(['Message' => __('resources.emptyCategories')], 404);
        return response()->json(['Categories' => $catergories], 200);
    }



}
