<?php

namespace App\Helpers;

use App\Models\Wallet;

class WalletHelper
{
  public static function createWallet($user_id){
      Wallet::create([
          'user_id'=>$user_id,
          'balance'=>1000,

      ]);
  }
}

