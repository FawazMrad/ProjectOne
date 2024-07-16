<?php

namespace App\Http\Controllers;

use App\Helpers\DateTimeHelper;
use App\Models\FillHistory;
use App\Models\Station;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StationController extends Controller
{
    public function stationSignup(Request $request)
    {
        $station = new Station;
        $station->name = $request->input('name');
        $station->password = Hash::make($request->input('password'));
        $station->location = $request->input('location');
        $station->manager_name = $request->input('managerName');
        $station->manager_email = $request->input('managerEmail');
        $station->manager_id_picture = $request->input('managerIdPicture');
        $station->balance = $request->input('balance');
        $station->governorate = $request->input('governorate');
        $station->save();

        return response()->json(['message' => 'Station registered successfully']);
    }

    public function stationDeposit(Request $request)
    {
        $userId = $request->input('userId');
        $walletId=User::find($userId)->wallet()->first()->id;
        $quantity = $request->input('quantity');
        $stationId = $request->input('stationId');
        $station = Station::find($stationId);
        $withdrawResult = self::canStationWithdraw($station, $quantity);
        if ($withdrawResult['status']) {
            $depositMessage = WalletController::depositStatic(0.05, $userId, $quantity);
           $fillHistory= FillHistory::create([
                'station_id'=>$stationId,
                'wallet_id'=>$walletId,
                'quantity'=>$quantity,
                'fill_date'=>DateTimeHelper::getCurrentDateTime()
            ]);
            $fillHistory->save();
            return response()->json(['message' => $depositMessage['message']], 200);
        }
        return response()->json(['message' => $withdrawResult['message']], 400);
    }

    public static function canStationWithdraw($station, $quantity)
    {
        $stationBalance = $station->balance;
        if ($stationBalance < $quantity) {
            return ['status' => false, 'message' => __('wallet.stationCannotWithdraw')];
        } else {
            $station->balance = $stationBalance - $quantity;
            $station->save();
            return ['status' => true];
        }
    }
    public function stationSignIn(Request $request)
    {
        $name = $request->input('name');
        $password = $request->input('password');
       $station = Station::where('name', $name)->first();
            if (!$station || !Hash::check($password, $station->password)) {
                return response()->json(['message' => __('auth.failed') ], 401);
            }
        return response()->json([
            'message' => __('auth.signInSuccess'),
            'station' => $station
        ], 200);
    }
    public function monthlyReport(Request $request){
        $stationId=$request->input('stationId');
         $station=Station::find($stationId);
         $days=DateTimeHelper::getFirstDayOfCurrentMonthAndLastDayOfAfterTowMonths();
        $depositHistory=$station->fillHistories()
            ->whereBetween('fill_date',[$days['firstDayOfCurrentMonth'],$days['lastDayOfCurrentMonth']])->get();
          if($depositHistory)
              return response()->json(['Fill History'=>$depositHistory],200);
              return response()->json(['message'=>__('wallet.emptyHistory')],404);

    }
}

