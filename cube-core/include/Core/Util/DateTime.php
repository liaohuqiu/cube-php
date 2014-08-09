<?php
/**
 *   日期时间
 *
 * @author      huqiu
 */
class MCore_Util_DateTime
{
    var $year;
    var $month;
    var $day;

    var $time;

    function __construct($time = null)
    {
        $time = self::toTimeStamp($time);
        !$time && $time = time();

        $this->year = date('Y', $time);
        $this->month = date('m', $time);
        $this->day = date('d', $time);

        $this->time = $time;
    }

    public static function create($time = null)
    {
        return new MCore_Util_DateTime($time);
    }

    public static function toTimeStamp($strOrTimeStamp)
    {
        if (!$strOrTimeStamp)
        {
            return false;
        }
        if (!is_numeric($strOrTimeStamp))
        {
            $strOrTimeStamp = strtotime($strOrTimeStamp);
            if (!$strOrTimeStamp || $strOrTimeStamp == -1)
            {
                return false;
            }
            return $strOrTimeStamp;
        }
        else
        {
            return $strOrTimeStamp;
        }
    }

    public function offset($offset)
    {
        $this->time += $offset;
        return $this;
    }

    function getTime()
    {
        return $this->time;
    }

    function getYear()
    {
        return $this->year;
    }

    function getMonth()
    {
        return $this->month;
    }

    function getDay()
    {
        return $this->day;
    }

    public static function now()
    {
        return new MCore_Util_DateTime();
    }

    public function __toString()
    {
        return date('Y-m-d H:i:s', $this->time);
    }

    public function format($format = "Y-m-d H:i:s")
    {
        return date($format, $this->time);
    }

    public function getDate()
    {
        $time = mktime(0,0,0, $this->month, $this->day, $this->year);
        return new MCore_Util_DateTime($time);
    }

    public function nextDay()
    {
        $time = mktime(0,0,0, $this->month, $this->day + 1, $this->year);
        return new MCore_Util_DateTime($time);
    }

    public function prevDay()
    {
        $time = mktime(0,0,0, $this->month, $this->day - 1, $this->year);
        return new MCore_Util_DateTime($time);
    }

    public function addDays($day)
    {
        $time = mktime(0,0,0, $this->month, $this->day + $day, $this->year);
        return new MCore_Util_DateTime($time);
    }

    public function prevMonth()
    {
        $time = mktime(0,0,0, $this->month - 1,1, $this->year);
        return new MCore_Util_DateMonth($time);
    }

    public function lastMonth()
    {
        return $this->prevMonth();
    }

    public function nextMonth()
    {
        $time = mktime(0,0,0, $this->month + 1,1, $this->year);
        return new MCore_Util_DateMonth($time);
    }

    /**
     * 1 ~ 7
     */
    public function nextWeekDay($weekDay, $includeToday = false)
    {
        $start = $includeToday ? 0 : 1;
        for ($i = $start; $i <= 7; $i++)
        {
            $time = mktime(0, 0, 0, $this->month, $this->day + $i, $this->year);
            if (date('N', $time) == $weekDay)
            {
                return new MCore_Util_DateMonth($time);
            }
        }
    }

    /**
     * 1 ~ 7
     */
    public function prevWeekDay($weekDay, $includeToday = false)
    {
        $start = $includeToday ? 0 : 1;
        for ($i = $start; $i <= 7; $i++)
        {
            $time = mktime(0, 0, 0, $this->month, $this->day - $i, $this->year);
            if (date('N', $time) == $weekDay)
            {
                return new MCore_Util_DateMonth($time);
            }
        }
    }

    public function addMonths($month)
    {
        $time = mktime(0,0,0, $this->month + $month, $this->day, $this->year);
        return new MCore_Util_DateMonth($time);
    }

    public function addYears($years)
    {
        $time = mktime(0,0,0, $this->month, $this->day, $this->year + $years);
        return new MCore_Util_DateTime($time);
    }

    public function getYearDate()
    {
        $time = mktime(0,0,0,1,1, $this->year);
        return new MCore_Util_DateTime($time);
    }

    public function getMonthDate()
    {
        $time = mktime(0,0,0, $this->month,1, $this->year);
        return new MCore_Util_DateTime($time);
    }

    public function toMonth()
    {
        return new MCore_Util_DateMonth($this->time);
    }
}
