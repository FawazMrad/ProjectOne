<?php

namespace App\Http\Controllers;

use App\Helpers\ResourcesHelper;

use App\Models\Category;
use App\Models\DecorationCategory;
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




    public function getAvailableResourcesWithQuantity(Request $request) // furniture, decorationItem, security
    {
        $resourceName = $request->input('resourceName');
        $resource = ucfirst($resourceName);
        $resourceSmallLetter = strtolower($resourceName);

        if (in_array($resourceName, ['decoration_item', 'decorationItem', 'decoration item'])) {
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

       if($resourceSmallLetter==='decoration_item'){
        $availableResources[$resourceSmallLetter . '_items'][] = [
            'item' => [
                'id' => $item->id,
                'category_id' => $item->category_id,
                'name' => $item->name,
                'image' => $item->image,
                'description' => $item->{'description_' . app()->getLocale()},
                'individual_cost' => $item->cost,
                'quantity' => $item->quantity
            ],
            'availableQuantity' => $availableQuantity,
        ];
            }else {
           $availableResources[$resourceSmallLetter . '_items'][] = [
               'item' => [$item],
               'availableQuantity' => $availableQuantity,
           ];
       }
    }

    return response()->json($availableResources, 200);
}



//    public function getAvailableCatering(Request $request)
//    {
//        $eventId=$request->input('eventId');
//        $eventRequiredAge=Event::find($eventId)->min_age;
//        // Food and Drink
//        $typeSmallLetter = strtolower($request->input('type'));
//        $type = ucfirst($request->input('type'));
//        $modelClass = "App\\Models\\$type";
//
//        // Check if the class exists and get all records
//        if (!class_exists($modelClass)) {
//            return response()->json(['message' => __('resource.cateringNotFound')], 404);
//        }
//
//        $getType = $modelClass::all();
//
//        // Check if the collection is empty
//        if ($getType->isEmpty()) {
//            return response()->json(['message' => __('resource.cateringNotFound')], 404);
//        }
//
//        $locale = app()->getLocale();
//        $availableItems = $getType->map(function ($item) use ($locale, $typeSmallLetter,$eventRequiredAge) {
//            // Initialize ageRequired to 0 for food items
//            $ageRequired = 0;
//
//            // Set ageRequired if the type is drink
//            if ($typeSmallLetter === 'drink') {
//                $ageRequired = $item->age_required;
//                if($eventRequiredAge<$ageRequired)
//                {
//
//                }
//
//            }
//
//            return [
//                'id' => $item->id,
//                'name' => $item->name,
//                'type' => $typeSmallLetter,
//                'description' => $item->{'description_' . $locale},
//                'individual_cost' => $item->cost,
//                'image' => $item->image,
//                'age_required' => $ageRequired,
//            ];
//        });
//
//        return response()->json([$type . ' available' => $availableItems], 200);
//    }
    public function getAvailableCatering(Request $request)
    {
        $eventId = $request->input('eventId');
        $eventRequiredAge = Event::find($eventId)->min_age;
        // Food and Drink
        $typeSmallLetter = strtolower($request->input('type'));
        $type = ucfirst($request->input('type'));
        $modelClass = "App\\Models\\$type";

        // Check if the class exists and get all records
        if (!class_exists($modelClass)) {
            return response()->json(['message' => __('resource.cateringNotFound')], 404);
        }

        $getType = $modelClass::all();

        // Check if the collection is empty
        if ($getType->isEmpty()) {
            return response()->json(['message' => __('resource.cateringNotFound')], 404);
        }

        $locale = app()->getLocale();
        $availableItems = $getType->filter(function ($item) use ($eventRequiredAge, $typeSmallLetter) {
            // Initialize ageRequired to 0 for food items
            $ageRequired = 0;

            // Set ageRequired if the type is drink
            if ($typeSmallLetter === 'drink') {
                $ageRequired = $item->age_required;
                // Skip this item if the event's required age is less than the drink's required age
                if ($eventRequiredAge < $ageRequired) {
                    return false;
                }
            }

            return true;
        })->map(function ($item) use ($locale, $typeSmallLetter) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'type' => $typeSmallLetter,
                'description' => $item->{'description_' . $locale},
                'individual_cost' => $item->cost,
                'image' => $item->image,
                'age_required' => $item->age_required ?? 0,
            ];
        });

        return response()->json([$type . ' available' => $availableItems], 200);
    }

    public function getCategories(Request $request)//EventCats or DecorationCats
    {//Categories
        $type = $request->input('type');
        if ($type === 'Decoration') {
            $categoriesWithTowLangs = DecorationCategory::all();
        } else {

            $categoriesWithTowLangs = Category::all();
        }
        $categories = $categoriesWithTowLangs->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->{'description_' . app()->getLocale()},
                'icon' => $category->icon
            ];
        });
        if (!$categories)
            return response()->json(['Message' => __('resources.emptyCategories')], 404);
        return response()->json(['Categories' => $categories], 200);
    }


}
