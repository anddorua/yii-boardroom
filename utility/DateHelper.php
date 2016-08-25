<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 29.05.16
 * Time: 23:17
 */

namespace app\utility;

use app\models\Employee;

class DateHelper
{
    public static function GetLastDayInMonth(\DateTime $date)
    {
        return self::GetLastDateInMonth($date)->format('j');
    }
    public static function GetLastDateInMonth(\DateTime $date)
    {
        $next_per = self::GetFirstDateInMonth($date);
        $next_per->add(new \DateInterval('P1M'));
        $next_per->sub(new \DateInterval('P1D'));
        return $next_per;
    }
    public static function GetNextDay(\DateTime $date)
    {
        $next_per = clone $date;
        $next_per->add(new \DateInterval('P1D'));
        return $next_per;
    }
    public static function DateOfDay(\DateTime $period, $day_number)
    {
        return (new \DateTime())->setDate($period->format('Y'), $period->format('n'), $day_number);
    }
    public static function GetFirstDateInMonth(\DateTime $period)
    {
        return (new \DateTime())
            ->setDate($period->format('Y'), $period->format('n'), 1)
            ->setTime(0, 0, 0);
    }
    public static function IsDateInSamePeriod(\DateTime $d1, \DateTime $d2)
    {
        return $d1->format('Y') == $d2->format('Y') && $d1->format('m') == $d2->format('m');
    }
    public static function FormatTimeAccordingRule(\DateTime $time, $hour_mode)
    {
        return $time->format($hour_mode == Employee::MODE_HOUR_12 ? 'g:ia' : 'G:i');
    }
}