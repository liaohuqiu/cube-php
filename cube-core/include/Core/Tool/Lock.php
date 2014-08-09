<?php
class MCore_Tool_Lock
{
    public static function getLock($name)
    {
        $lockfp = fopen($name, 'w');
        if (!$lockfp)
        {
            return false;
        }
        if (!flock($lockfp, LOCK_EX | LOCK_NB))
        {
            return false;
        }
        return $lockfp;
    }
}
