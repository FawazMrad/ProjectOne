<?php

namespace App\Helpers;

use Carbon\Carbon;
use DateTime;

class DateTimeHelper
{
  public static function getCurrentDateTime(){

      return new \DateTime( Carbon::now());
  }

}
