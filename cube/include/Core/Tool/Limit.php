<?php
class MCore_Tool_Limit
{
    public static function testAdd($key, $max, $add = 1)
    {
        $current = self::getCount($key);
        return ($current + $add) <= $max;
    }

    public static function addAndTest($key, $max, $add = 1)
    {
        $current = MCore_Tool_Cache::getCacheProxy()->increment($key, $add);
        return $current <= $max;
    }

    public static function delete($key)
    {
        return MCore_Tool_Cache::getCacheProxy()->delete($key);
    }

    public static function setCount($key, $num)
    {
        return MCore_Tool_Cache::getCacheProxy()->set($key, $num);
    }

    public static function getCount($key)
    {
        return (int)MCore_Tool_Cache::getCacheProxy()->get($key);
    }

    public static function testAddDaily($key, $max, $add = 1)
    {
        return self::testAdd(self::getDailyKey($key), $max, $add);
    }

    public static function addAndTestDaily($key, $max, $add = 1)
    {
        return self::addAndTest(self::getDailyKey($key), $max, $add);
    }

    public static function deleteDaily($key)
    {
        return self::delete(self::getDailyKey($key));
    }

    public static function setCountDaily($key, $num)
    {
        return self::setCount(self::getDailyKey($key), $num);
    }

    public static function getCountDaily($key)
    {
        return self::getCount(self::getDailyKey($key));
    }

    private static function getDailyKey($key)
    {
        return $key . '_' . date('Ymd');
    }
}
