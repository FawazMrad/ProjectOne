<?php

namespace App\Helpers;

use Carbon\Carbon;
use DateTime;

class DateTimeHelper
{
    public static function userAge($userBirthDate,){
        $birthDate = new DateTime($userBirthDate);
        $currentDate = self::getCurrentDateTime();
        $age = $currentDate->diff($birthDate)->y;
        return $age;
    }
  public static function getCurrentDateTime(){

      return new \DateTime( Carbon::now());
  }
  public static function getFirstDayOfCurrentMonthAndLastDayOfAfterTowMonths(){
      $currentDateTime = self::getCurrentDateTime();
      $firstDayOfCurrentMonth = clone $currentDateTime;
      $firstDayOfCurrentMonth->modify('first day of this month')->setTime(0,0,0);
      $lastDayOfCurrentMonth=clone $currentDateTime;
      $lastDayOfCurrentMonth->modify('last day of this month')->setTime(23,59,59);
      $twoMonthsAhead = clone $currentDateTime;
      $twoMonthsAhead->modify('+2 months');
      $lastDayOfTwoMonthsAhead = clone $twoMonthsAhead;
      $lastDayOfTwoMonthsAhead->modify('last day of this month')->setTime(23,59,59);
      return[
          'firstDayOfCurrentMonth'=>$firstDayOfCurrentMonth,
          'lastDayOfCurrentMonth'=>$lastDayOfCurrentMonth,
          'lastDayOfTwoMonthsAhead'=>$lastDayOfTwoMonthsAhead
      ];
  }

}
