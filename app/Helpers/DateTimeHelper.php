<?php

namespace App\Helpers;

use Carbon\Carbon;
use DateTime;
use InvalidArgumentException;

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
    public static function getDateTime($type) {
        $currentDateTime = self::getCurrentDateTime();
        $dateTime = clone $currentDateTime;

        switch ($type) {
            case 'todayStart':
                $dateTime->modify('today')->setTime(0, 0, 0);
                break;
            case 'tomorrowEnd':
                $dateTime->modify('tomorrow')->setTime(23, 59, 59);
                break;
            default:
                throw new InvalidArgumentException("Invalid type provided. Use 'todayStart' or 'tomorrowEnd'.");
        }

        return $dateTime;
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
