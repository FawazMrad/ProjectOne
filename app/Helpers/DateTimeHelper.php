<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateTimeHelper
{
  public static function getCurrentDateTime(){
      return new \DateTime( Carbon::now());
  }
}
