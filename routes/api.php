<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/events/step1', [EventController::class, 'storeStep1']);//FirstStepOfEVENT-Creation
Route::post('/events/step2', [EventController::class, 'storeStep2']);//SecondStepOfEVENT-Creation
Route::post('/events/remove', [EventController::class, 'remove']);//Remove events in the body we have a parameter named desire it's values= delete , cancel
Route::post('/resources/available', [ResourceController::class, 'getAvailableResources']);//getTheAvailableResources(Venues,Sound)
Route::post('/resources/available/quantity', [ResourceController::class, 'getAvailableResourcesWithQuantity']);//getTheAvailableDecorationItems,security,furniture{"eventId": ,"resourceName":}
Route::post('/resources/available/catering',[ResourceController::class,'getAvailableCatering']);//get The available food and drink {header named type}
Route::post('/resources/categories',[ResourceController::class,'getCategories']  ); //get the event categories and decoration items categories
Route::post('/events/getPrices',[EventController::class,'getPrices']  ); //get the event's tickets prices
Route::post('/events/adjustPrices',[EventController::class,'adjustPrices']  ); //adjust the event's tickets prices
Route::post('/wallet/deposit',[WalletController::class,'deposit']  ); //add money to the user's wallet
Route::post('/wallet/balance',[WalletController::class,'getWalletBalance']  ); // user's wallet balance
Route::post('/wallet/gift',[WalletController::class,'gift']  ); //send money gifts
Route::post('/events/getEventReservations',[EventController::class,'getEventReservations']  ); //(first step for event update)get event's reservations
Route::post('/events/updateEventQuantitiesReservations',[EventController::class,'updateEventQuantitiesReservations']  ); //(second step for event update)// furniture, decoration item , security ,food ,drink {for food and drink I want a servingDate}
Route::post('/events/updateEventSoundAndVenueReservations',[EventController::class,'updateEventSoundAndVenueReservations']  ); //(second step for event update)// sound, venue {If you want to delete a previous reservation just give it to me like what you give me to create a new reservation}


