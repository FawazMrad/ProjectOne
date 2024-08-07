<?php

namespace App\Http\Controllers;

use App\Helpers\DateTimeHelper;
use App\Helpers\WalletHelper;
use App\Models\GiftHistory;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public static function refund($userId, $creatorId, $quantity)
    {
        $wallet = Wallet::where('user_id', $creatorId)->first();
        $newBalance = $wallet->balance - $quantity;
        $wallet->balance = $newBalance;
        $wallet->save();
        self::depositStatic(0.02, $userId, $quantity);
        return ['message' => __('wallet.giftOk'), 'status' => true];
    }

    public static function depositStatic($rate, $ownerId, $quantity)
    {
        if ($rate != 1) $quantity = $quantity - ($quantity * $rate);

        $wallet = Wallet::where('user_id', $ownerId)->first();
        $newBalance = self::getWalletBalanceStatic($wallet) + $quantity;
        $wallet->balance = $newBalance;
        $wallet->save();

        return ['status' => true, 'message' => __('wallet.depositSuccess')];
    }

    public static function getWalletBalanceStatic($wallet)
    {
        $balance = $wallet->balance;
        return $balance;
    }

    public function recentTransactions(Request $request)
    {
        $user = $request->user();
        $wallet = $user->wallet();

        $gifts = WalletHelper::getGifts($user);
        $sentGifts = $gifts['sentGifts'];
        $receivedGifts = $gifts['receivedGifts'];

        $deposits = WalletHelper::getDeposits($wallet);

        $sentGiftsCollection = collect($sentGifts);
        $receivedGiftsCollection = collect($receivedGifts);
        $depositsCollection = collect($deposits);

        $allTransactions = $sentGiftsCollection->merge($depositsCollection)->merge($receivedGiftsCollection);

        return response()->json(['All transactions' => $allTransactions], 200);
    }

    public function gift(Request $request)
    {
        $senderId = $request->user()->id;
        $receiverId = $request->input('receiverId');
        $quantity = $request->input('quantity');
        $result = self::giftStatic($senderId, $receiverId, $quantity);
        if ($result['status']) self::createGiftObject($senderId, $receiverId, $quantity);
        return response()->json($result);

    }

    public static function giftStatic($senderId, $receiverId, $quantity)
    {
        $userCanPay = self::canUserPay($senderId, $quantity);
        if ($userCanPay['status'] === true) {
            self::withdraw($senderId, $quantity);
            self::depositStatic(1, $receiverId, $quantity);
            return ['message' => __('wallet.giftOk'), 'status' => true];
        }
        return ['message' => __('wallet.cannotWithdraw'), 'status' => false];
    }

    public static function canUserPay($ownerId, $quantity)
    {
        $wallet = Wallet::where('user_id', $ownerId)->first();
        $balance = self::getWalletBalanceStatic($wallet);
        if ($quantity > $balance) {
            return ['wallet' => $wallet, 'status' => false, 'message' => __('wallet.cannotWithdraw')];
        }
        return ['wallet' => $wallet, 'status' => true, 'message' => __('wallet.withdrawSuccess')];

    }

    public static function withdraw($ownerId, $quantity)
    {
        $userCanPay = self::canUserPay($ownerId, $quantity);
        $wallet = $userCanPay['wallet'];
        if ($userCanPay['status']) {
            $newBalance = $wallet->balance - $quantity;
            $wallet->balance = $newBalance;
            $wallet->save();
            return ['status' => $userCanPay['status'], 'message' => $userCanPay['message']];
        }
        return ['status' => $userCanPay['status'], 'message' => $userCanPay['message']];
    }

    public static function createGiftObject($senderId, $receiverId, $quantity)
    {
        $giftObject = GiftHistory::create(['sender_id' => $senderId, 'receiver_id' => $receiverId, 'quantity' => $quantity]);
        $giftObject->save();

    }

    public function getWalletBalance(Request $request)
    {
        $user = $request->user();
        $wallet = $user->wallet()->first();
        $balance = self::getWalletBalanceStatic($wallet);
        return response()->json(['balance' => $balance], 200);
    }

    public function deposit(Request $request)
    {
        $ownerId = $request->input('ownerId');
        $quantity = $request->input('quantity');

        $result = self::depositStatic(1, $ownerId, $quantity);

        return response()->json($result);
    }
    public function getUserPointsWithFactor(Request $request){
        $user=$request->user();
        $userPoints=$user->points;
        $factor=WalletHelper::getFactor();
        return response()->json(['points'=>$userPoints,'factor'=>$factor],200);
    }
    public function exchangePoints (Request $request){
        $pointsToExchange=$request->input('points');
        $factor=WalletHelper::getFactor();
        $amount=$pointsToExchange * $factor;
        $user=$request->user();
        $userId=$user->id;
        $user->points-=$pointsToExchange;
        $depositResult= self::depositStatic(1, $userId, $amount);
        $user->save();
        return response()->json(['message'=>$depositResult['message'],'amount'=>$amount],200);
    }

}
