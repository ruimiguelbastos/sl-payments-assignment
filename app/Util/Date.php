<?php

namespace App\Util;
use Carbon\Carbon;

final class Date
{
    public static function isSameMonthFromSameYear(Carbon $date1, Carbon $date2): bool
    {
        return $date1->month === $date2->month && $date1->year === $date2->year; 
    }
}
