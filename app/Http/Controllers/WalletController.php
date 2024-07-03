<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{

    public function giftStatic($senderId, $receiverId, $quantity)
    {
        $senderWallet = Wallet::where('user_id', $senderId)->first();
        $receiverWallet = Wallet::where('user_id', $receiverId)->first();
        $senderBalance = $this->getWalletBalanceStatic($senderWallet);
        if ($quantity > $senderBalance) {
            return ['message' => __('wallet.cannotWithdraw'),'status'=>false];
        }
        $this->withdraw($senderId, $quantity);
        $this->depositStatic(1,$receiverId, $quantity);
        return ['message' => __('wallet.giftOk'),'status'=>true];
    }
    public function gift(Request $request){
        $senderId=$request->input('senderId');
        $receiverId=$request->input('receiverId');
        $quantity=$request->input('quantity');
       $result= self::giftStatic($senderId,$receiverId,$quantity);
       return response()->json($result);

    }

    public static function getWalletBalanceStatic($wallet)
    {
        $balance = $wallet->balance;
        return $balance;
    }

    public function getWalletBalance(Request $request){
        $wallet=Wallet::where('user_id',$request->input('ownerId'))->first();
        $balance=self::getWalletBalanceStatic($wallet);
        return response()->json(['balance'=>$balance],200);
    }

    public static function canUserPay($ownerId,$quantity){
        $wallet = Wallet::where('user_id', $ownerId)->first();
        $balance = self::getWalletBalanceStatic($wallet);
        if ($quantity > $balance) {
            return [
                'wallet'=>$wallet,
                'status' => false,
                'message' => __('wallet.cannotWithdraw')
            ];
        }
        return [
            'wallet'=>$wallet,
            'status' => true,
            'message' => __('wallet.withdrawSuccess')
        ];

    }
    public static function withdraw($ownerId, $quantity)
    {
        $userCanPay=self::canUserPay($ownerId,$quantity);
        $wallet=$userCanPay['wallet'];
        if($userCanPay['status']) {
            $newBalance = $wallet->balance - $quantity;
            $wallet->balance = $newBalance;
            $wallet->save();
            return [
                'status' => $userCanPay['status'],
                'message' => $userCanPay['message']
            ];
        }
        return [
            'status' => $userCanPay['status'],
            'message' => $userCanPay['message']
        ];
    }

    public function deposit(Request $request)
    {
        $ownerId = $request->input('ownerId');
        $quantity = $request->input('quantity');

        $result = self::depositStatic(1, $ownerId, $quantity);

        return response()->json($result);
    }

    public static function depositStatic($rate, $ownerId, $quantity)
    {
        if($rate!= 1)
        $quantity = $quantity - ($quantity * $rate);

        $wallet = Wallet::where('user_id', $ownerId)->first();
        $newBalance = self::getWalletBalanceStatic($wallet) + $quantity;
        $wallet->balance = $newBalance;
        $wallet->save();

        return [
            'status' => true,
            'message' => __('wallet.depositSuccess')
        ];
    }
}
