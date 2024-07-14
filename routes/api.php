<?php

use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\FriendshipController;
use App\Http\Controllers\User\PreferenceController;
use App\Http\Controllers\User\UserController;
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

Route::post('/users/signUp', [AuthController::class, 'signUp']);//for user signup
Route::post('/users/signIn', [AuthController::class, 'signIn']);//for user traditional signIn
Route::post('/users/googleSignIn', [AuthController::class, 'googleSignIn']);//for user google signIn

Route::post('/events/getEventsByCategory', [EventController::class, 'getEventsByCategory']); //getThe events by their categories
Route::post('/events/searchEvents', [EventController::class, 'searchEvents']); //getThe events by : categoryId,startDate&endDate,location,minAge,priceRange(10-20)
Route::get('/events/mostPopularEvents', [EventController::class, 'mostPopularEvents']); //getThe most popular events
Route::post('/events/getEvent', [EventController::class, 'getEvent']); //getThe desired event
Route::post('/users/getUser', [UserController::class, 'getUser']); //getThe desired user



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/events/step1', [EventController::class, 'storeStep1']);//FirstStepOfEVENT-Creation
    Route::post('/users/signOut', [AuthController::class, 'signOut']);//for user sign out
    Route::post('/events/step2', [EventController::class, 'storeStep2']);//SecondStepOfEVENT-Creation
    Route::post('/events/remove', [EventController::class, 'remove']);//Remove events in the body we have a parameter named desire it's values= delete , cancel
    Route::post('/resources/available', [ResourceController::class, 'getAvailableResources']);//getTheAvailableResources(Venues,Sound)
    Route::post('/resources/available/quantity', [ResourceController::class, 'getAvailableResourcesWithQuantity']);//getTheAvailableDecorationItems,security,furniture{"eventId": ,"resourceName":}
    Route::post('/resources/available/catering', [ResourceController::class, 'getAvailableCatering']);//get The available food and drink {header named type}
    Route::post('/resources/categories', [ResourceController::class, 'getCategories']); //get the event categories and decoration items categories
    Route::post('/events/getPrices', [EventController::class, 'getPrices']); //get the event's tickets prices
    Route::post('/events/adjustPrices', [EventController::class, 'adjustPrices']); //adjust the event's tickets prices

    //for wallet
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']); //add money to the user's wallet
    Route::get('/wallet/balance', [WalletController::class, 'getWalletBalance']); // user's wallet balance
    Route::post('/wallet/gift', [WalletController::class, 'gift']); //send money gifts
   //update event
    Route::post('/events/getEventReservations', [EventController::class, 'getEventReservations']); //(first step for event update)get event's reservations
    Route::post('/events/updateEventQuantitiesReservations', [EventController::class, 'updateEventQuantitiesReservations']); //(second step for event update)// furniture, decoration item , security ,food ,drink {for food and drink I want a servingDate}
    Route::post('/events/updateEventSoundAndVenueReservations', [EventController::class, 'updateEventSoundAndVenueReservations']); //(second step for event update)// sound, venue {If you want to delete a previous reservation just give it to me like what you give me to create a new reservation}
    //for friendships
    Route::post('/users/sendFollowRequest', [FriendshipController::class, 'sendFollowRequest']);//for sending follow requests
    Route::post('/users/blockUser', [FriendshipController::class, 'blockUser']);//for blocking requests
    Route::post('/users/cancelFollowing', [FriendshipController::class, 'cancelFollowing']);//for canceling following
    Route::get('/users/getFollowers', [FriendshipController::class, 'getFollowers']);//for getting followers
    Route::get('/users/getFollowing', [FriendshipController::class, 'getFollowing']);//for getting Following
    Route::get('/users/getBlocked', [FriendshipController::class, 'getBlocked']);//for getting blocked
// for preferences
    Route::get('/users/getPreferences', [PreferenceController::class, 'getPreferences']);//for get the user's preferences
    Route::post('/users/adjustPreferences', [PreferenceController::class, 'adjustPreferences']);//for adjust the user's preferences one by one

/// for the favourites
    Route::post('/users/changeEventFavouriteState', [FavouriteController::class, 'addOrRemoveFavourite']);//for add the event to the favourite list
    Route::post('/users/getFavouriteEvents', [FavouriteController::class, 'getUserFavourites']);//for getting the user's fav events
/// for the attendee
    Route::post('/attendees/sendInvitation', [AttendeeController::class, 'sendInvitation']);//for sendInvitation
    Route::post('/attendees/confirmInvitation', [AttendeeController::class, 'confirmInvitation']);//for confirm the invitation
    Route::post('/attendees/cancelInvitation', [AttendeeController::class, 'cancelInvitation']);//for cancel the invitation
    Route::post('/attendees/purchaseTicket', [AttendeeController::class, 'purchaseTicket']);//for purchase Tickets
    Route::post('/attendees/cancelTicket', [AttendeeController::class, 'cancelTicket']);//for cancel Tickets
    Route::post('/attendees/checkIn', [AttendeeController::class, 'checkIn']);//for check in Tickets
//for profile
    Route::get('/users/getProfile', [UserController::class, 'getProfile']);//for getProfile
    Route::get('/users/getAttendedEvents', [UserController::class, 'getAttendedEvents']);//for getAttended Events
    Route::get('/users/eventsHistory', [UserController::class, 'eventsHistory']);//for events History
    Route::get('/users/getUpComingEvents', [UserController::class, 'getUpComingEvents']);//for getUpComingEvents
    Route::get('/users/searchUsers', [UserController::class, 'searchUsers']);//for searchUsers
    Route::post('/users/editProfile', [UserController::class, 'editProfile']);//for editProfile

});
