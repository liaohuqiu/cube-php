<?php
/**
 *  month
 *
 * @author      huqiu
 */
class MCore_Util_DateMonth extends MCore_Util_DateTime
{
    function __construct($time = null)
    {
        parent::__construct($time);
    }

    public function lastWeekDay($weekDay)
    {
        $begin_day = date('d', $this->firstDay()->time);
        $end_day = date('d', $this->lastDay()->time);
        for($i = $end_day; $i >= $begin_day; $i--)
        {
            $time = mktime(0, 0, 0, $this->month, $i, $this->year);
            if(date('N', $time) == $weekDay)
            {
                return new MCore_Util_DateMonth($time);
            }
        }
    }

    public function weekDays($weekDay)
    {
        $begin_day = date('d', $this->firstDay()->time);
        $end_day = date('d', $this->lastDay()->time);
        $days = array();
        for($i = $begin_day; $i <= $end_day; $i++)
        {
            $time = mktime(0, 0, 0, $this->month, $i, $this->year);
            if(date('N', $time) == $weekDay)
            {
                $days[] = new MCore_Util_DateMonth($time);
            }
        }
        return $days;
    }

    public function firstWeekDay($weekDay)
    {
        $begin_day = date('d', $this->firstDay()->time);
        $end_day = date('d', $this->lastDay()->time);
        for ($i = $begin_day; $i <= $end_day; $i++)
        {
            $time = mktime(0, 0, 0, $this->month, $i, $this->year);
            if (date('N', $time) == $weekDay)
            {
                return new MCore_Util_DateMonth($time);
            }
        }
    }

    public function firstDay()
    {
        $time = mktime(0, 0, 0, $this->month, 1, $this->year);
        return new MCore_Util_DateMonth($time);
    }

    public function lastDay()
    {
        $time = mktime(0, 0, 0, $this->month + 1, 0, $this->year);
        return new MCore_Util_DateMonth($time);
    }

    public function days()
    {
        return ($this->lastDay()->getTime() - $this->getTime()) / 86400 + 1;
    }
}
