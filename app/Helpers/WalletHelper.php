<?php

namespace App\Helpers;

use App\Models\Wallet;

class WalletHelper
{
    public static function getFactor(){
        $factor=10;
        return $factor;
    }
  public static function createWallet($user_id){
      Wallet::create([
          'user_id'=>$user_id,
          'balance'=>1000,

      ]);
  }
  public static function makeArrayAndAddAttributesToIt($things,$state)
  {
      $things = $things->map(function ($thing) use($state){
          $thingArray = $thing->toArray();
          $thingArray['state'] = $state;
          return $thingArray;
      });
      return $things;
  }
    public  static function getGifts($user){
        $receivedGifts=$user->receivedGifts()->get();
       $receivedGifts=self::makeArrayAndAddAttributesToIt($receivedGifts,'RECEIVED');
        $sentGifts=$user->sentGifts()->get();
      $sentGifts=self::makeArrayAndAddAttributesToIt($sentGifts,'SENT');
            return ['receivedGifts'=>$receivedGifts,'sentGifts'=>$sentGifts];
    }
    public static function getDeposits($wallet){

      $fillHistories=$wallet->first()->fillHistories()->get();
        $fillHistories = self::makeArrayAndAddAttributesToIt($fillHistories,'FILLED');
      return $fillHistories;
    }
}

