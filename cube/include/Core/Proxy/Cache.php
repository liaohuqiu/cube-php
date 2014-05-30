<?php
/**
 * 缓存工具，提供常用的几个方法以及进程内缓存
 */
class MCore_Proxy_Cache
{
    private static $cacheList = array();

    private static function getCacheProxy()
    {
        return MCore_Min_RedisCache::getInstance();
        // return \MCore_Proxy_MCache::getInstance();
    }

    /**
     * 获取值
     */
    public static function get($key)
    {
        if (isset(self::$cacheList[$key]))
        {
            return self::$cacheList[$key];
        }
        return self::getCacheProxy()->get($key);
    }

    /*
     * 设置值
     */
    public static function set($key, $value, $expired = 0)
    {
        // 除非60秒内的缓存，其他都入进程间缓存
        if ($expired == 0 || $expired > 60)
        {
            self::$cacheList[$key] = $value;
        }
        return self::getCacheProxy()->set($key, $value, $expired);
    }

    /**
     * 批量获取，返回 关联数组
     */
    public static function getMulti($keys)
    {
        $list = array();
        $unHitKeys = array();
        foreach ($keys as $key)
        {
            if (isset(self::$cacheList[$key]))
            {
                $list[$key] = self::$cacheList[$key];
            }
            else
            {
                $unHitKeys[] = $key;
            }
        }


        if (!empty($unHitKeys))
        {
            $cacheList = self::getCacheProxy()->getMulti($unHitKeys);
            $list = $list + $cacheList;

        }
        return $list;
    }

    /**
     * 设置数组
     */
    public static function setObj($key, $value, $expired = 0)
    {
        // 除非60秒内的缓存，其他都入进程间缓存
        if ($expired == 0 || $expired > 60)
        {
            self::$cacheList[$key] = $value;
        }
        return self::getCacheProxy()->setObj($key, $value, $expired);
    }

    /**
     * 获取数组
     */
    public static function getObj($key)
    {
        if (isset(self::$cacheList[$key]))
        {
            return self::$cacheList[$key];
        }
        return self::getCacheProxy()->getObj($key);
    }

    /**
     * 批量获取数组
     */
    public static function getMultiObj($keys)
    {
        $list = array();
        $unHitKeys = array();
        foreach ($keys as $key)
        {
            if (isset(self::$cacheList[$key]))
            {
                $list[$key] = self::$cacheList[$key];
            }
            else
            {
                $unHitKeys[] = $key;
            }
        }

        if (!empty($unHitKeys))
        {
            $cacheList = self::getCacheProxy()->getMultiObj($unHitKeys);
            $list = $list + $cacheList;

        }
        return $list;
    }

    public static function delete($key)
    {
        unset(self::$cacheList[$key]);
        return self::getCacheProxy()->delete($key);
    }

    public static function fetch($cacheKey, $getFn, $expired = 0)
    {
        $data === false;
        if ($expired >= 0)
        {
            $data = \Tool\Cache::getObj($cacheKey);
        }
        if ($data === false)
        {
            $data = call_user_func($getFn);
            \Tool\Cache::setObj($cacheKey, $data, $expired);
        }
        return $data;
    }

    public static function getSmartyList($ids, $getKeyFn, $getListFn, $expired = 0)
    {
        $keys = array();
        foreach ($ids as $id)
        {
            $keys[$id] = call_user_func($getKeyFn, $id);
        }

        $cacheList = array();
        if ($expired >= 0)
        {
            $cacheList = self::getMultiObj($keys);
        }
        $unHitIds = array();
        $list = array();
        foreach ($keys as $id => $key)
        {
            if (!isset($cacheList[$key]) || $cacheList[$key] === false)
            {
                $unHitIds[] = $id;
            }
            else
            {
                $list[$id] = $cacheList[$key];
            }
        }

        if (!empty($unHitIds))
        {
            $rawList = call_user_func($getListFn, $unHitIds);
            foreach ($unHitIds as $id)
            {
                if (isset($rawList[$id]))
                {
                    $item = $rawList[$id];
                    $list[$id] = $item;
                    $cacheKey = call_user_func($getKeyFn, $id);
                    self::setObj($cacheKey, $item, $expired);
                }
            }
        }

        return $list;
    }
}
