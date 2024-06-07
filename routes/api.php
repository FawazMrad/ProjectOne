<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ResourceController;
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
Route::post('/resources/available', [ResourceController::class, 'getAvailableResources']);//getTheAvailableResources(Venues,Sound)
Route::post('/resources/available/quantity', [ResourceController::class, 'getAvailableResourcesWithQuantity']);//getTheAvailableDecorationItems,security,furniture{"eventId": ,"resourceName":}
Route::get('/resources/available/catering',[ResourceController::class,'getAvailableCatering']);//get The available food and drink {header named type}
Route::get('/resources/categories',[ResourceController::class,'getCategories']  );

