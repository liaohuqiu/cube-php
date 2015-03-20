<?php
/**
 * The value `false` can not be cached directly
 *
 * @author liaohuqiu@gmail.com
 */
class MCore_Tool_Cache
{
    private static $proxy; // MCore_Proxy_IMCache
    private static $cacheList = array();

    const MASK_ONLY_LOCAL_CACHE = 0x02;
    const MASK_USE_LOCAL_CACHE = 0x03;
    const FLAG_USE_LOCAL_CACHE = 0x01;
    const FLAG_ONLY_LOCAL_CACHE = 0x02;

    /**
     * return an instance of cache proxy
     */
    public static function getCacheProxy()
    {
        if (!self::$proxy)
        {
            if (!function_exists('fn_getCacheProxy'))
            {
                throw new MCore_Proxy_Exception('fn_getCacheProxy undefined');
            }
            self::$proxy = fn_getCacheProxy($fn);
        }
        return self::$proxy;
    }

    public static function get($key, $localCacheTag = self::FLAG_USE_LOCAL_CACHE)
    {
        if (!MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog('cache', 'get: ' . $key);
        }
        $useLocalCache = $localCacheTag & self::MASK_USE_LOCAL_CACHE;
        $onlyLocalCache = $localCacheTag & self::MASK_ONLY_LOCAL_CACHE;
        if ($useLocalCache && isset(self::$cacheList[$key]))
        {
            return self::$cacheList[$key];
        }

        if ($onlyLocalCache)
        {
            return false;
        }
        $ret = self::getCacheProxy()->get($key);
        // cache even the $ret is false;
        if ($useLocalCache && $ret !== false)
        {
            self::$cacheList[$key] = $ret;
        }
        return $ret;
    }

    /**
     * set $expire to lower than 0, will call delete before set
     */
    public static function set($key, $value, $expire = 0)
    {
        if ($value === false)
        {
            return false;
        }
        if (!MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog('cache', 'set: ' . $key);
        }
        if ($expire == -1)
        {
            return self::delete($key);
        }
        unset(self::$cacheList[$key]);
        return self::getCacheProxy()->set($key, $value, $expire);
    }

    /**
     * return an array indexed by key
     */
    public static function getMulti($keys, $localCacheTag = self::FLAG_USE_LOCAL_CACHE)
    {
        $list = array();
        $unHitKeys = array();
        $useLocalCache = $localCacheTag & self::MASK_USE_LOCAL_CACHE;
        $onlyLocalCache = $localCacheTag & self::MASK_ONLY_LOCAL_CACHE;
        if ($useLocalCache)
        {
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
        }
        else
        {
            $unHitKeys = $keys;
        }

        if ($onlyLocalCache)
        {
            return $list;
        }

        if (!empty($unHitKeys))
        {
            $cacheList = self::getCacheProxy()->getMulti($unHitKeys);
            $list = $list + $cacheList;

            if ($useLocalCache && is_array($cacheList))
            {
                foreach ($cacheList as $key => $item)
                {
                    self::$cacheList[$key] = $item;
                }
            }
        }

        return $list;
    }

    /**
     * set value to cache, value can be an array, but can not be object.
     * set $expire to -1, will call delete before set
     * set $expire to 0, will never expire
     */
    public static function setObj($key, $value, $expire = 0)
    {
        if ($value === false)
        {
            return false;
        }
        if (!MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog('cache', 'setObj: ' . $key);
        }
        if ($expire == -1)
        {
            return self::delete($key);
        }
        unset(self::$cacheList[$key]);
        return self::getCacheProxy()->setObj($key, $value, $expire);
    }

    /**
     * get value/ array by $key from localCache and cache
     *
     * You can store the data in process cache by set $localCacheTag
     *
     * the data will be cached in process cache.
     *
     * When $localCacheTag is set to true and you can filter the value by $onToLocalFn
     */
    public static function getObj($key, $localCacheTag = self::FLAG_USE_LOCAL_CACHE, $onToLocalFn = null)
    {
        $useLocalCache = $localCacheTag & self::MASK_USE_LOCAL_CACHE;
        $onlyLocalCache = $localCacheTag & self::MASK_ONLY_LOCAL_CACHE;
        if ($useLocalCache && isset(self::$cacheList[$key]))
        {
            return self::$cacheList[$key];
        }
        if ($onlyLocalCache)
        {
            return false;
        }
        if (!MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog('cache', 'getObj: ' . $key);
        }
        $ret = self::getCacheProxy()->getObj($key);
        if ($ret !== false && $useLocalCache)
        {
            if ($onToLocalFn)
            {
                $ret = call_user_func($onToLocalFn, $ret);
            }
            self::$cacheList[$key] = $ret;
        }
        return $ret;
    }

    /**
     * batch get a list or array, indexed by key
     */
    public static function getMultiObj($keys, $localCacheTag = self::FLAG_USE_LOCAL_CACHE, $onToLocalFn = null)
    {
        $useLocalCache = $localCacheTag & self::MASK_USE_LOCAL_CACHE;
        $onlyLocalCache = $localCacheTag & self::MASK_ONLY_LOCAL_CACHE;
        if (!MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog('cache', 'getMultiObj: ' . implode(',', $keys));
        }
        $list = array();
        $unHitKeys = array();
        if ($useLocalCache)
        {
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
        }
        else
        {
            $unHitKeys = $keys;
        }

        if ($onlyLocalCache)
        {
            return $list;
        }

        if (!empty($unHitKeys))
        {
            $cacheList = self::getCacheProxy()->getMultiObj($unHitKeys);
            if (is_array($cacheList) && !empty($cacheList))
            {
                foreach ($cacheList as $key => $item)
                {
                    if ($useLocalCache)
                    {
                        if ($onToLocalFn)
                        {
                            $item = call_user_func($onToLocalFn, $key, $item);
                            if ($item === false)
                            {
                                continue;
                            }
                        }
                        self::$cacheList[$key] = $item;
                    }
                    $list[$key] = $item;
                }
            }
        }

        // reset order
        $ret = array();
        foreach ($keys as $key)
        {
            if (isset($list[$key]))
            {
                $ret[$key] = $list[$key];
            }
        }
        return $ret;
    }

    /**
     * delete data from cache and local cache
     */
    public static function delete($key)
    {
        if (!MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog('cache', 'delete: ' . $key);
        }
        unset(self::$cacheList[$key]);
        return self::getCacheProxy()->delete($key);
    }

    /**
     * fetch value by $cacheKey, the value will cache to process cache.
     *
     * The data return by $getFn will be cached into process cache
     *
     * $onToLocalFn($info) filter the data
     *
     * $expire lower than 0 will disable cache, data will not be set to mcache
     *
     * $data will be always set to local cache
     *
     * set $expire to 0, will use local cache only, and data will not be set to mcache
     */
    public static function fetch($cacheKey, $getFn, $onToLocalFn = null, $expire = 0)
    {
        $data = false;

        if ($expire >= 0)
        {
            // cache time set to 0, local cache will be used only
            $flag = $expire == 0 ? self::FLAG_ONLY_LOCAL_CACHE : self::FLAG_USE_LOCAL_CACHE;
            $data = self::getObj($cacheKey, $flag, $onToLocalFn);
        }

        if ($data === false)
        {
            $data = call_user_func($getFn);
            if ($data !== false && $expire != 0)
            {
                self::setObj($cacheKey, $data, $expire);
            }
            if ($onToLocalFn)
            {
                $data = call_user_func($onToLocalFn, $data);
            }

            // always set to local cache
            if ($data !== false)
            {
                self::$cacheList[$cacheKey] = $data;
            }
        }
        return $data;
    }

    /**
     * like #fetch()
     *
     * this function fetchs all the values by given $ids, if the value is not in cache, $getListFn will be called to get value
     *
     * $getListFn($ids)
     *
     * onToLocalFn($id, $item)
     */
    public static function fetchList($ids, $getKeyFn, $getListFn, $onToLocalFn = null, $expire = 0, $default = false)
    {
        if (empty($ids))
        {
            return [];
        }
        $isArray = is_array($ids);
        if (!$isArray)
        {
            $singleId = $ids;
            $ids = (array)$ids;
        }
        $ids = array_filter(array_unique($ids));
        $keys = array();
        foreach ($ids as $id)
        {
            if (is_array($id))
            {
                throw new Exception('The key of each item should not be array');
            }
            $keys[$id] = call_user_func($getKeyFn, $id);
        }

        $cacheList = array();
        if ($expire >= 0)
        {
            $onToLocalFnProxy = null;
            if ($onToLocalFn != null)
            {
                $keys_filp = array_flip($keys);
                $onToLocalFnProxy = function($key, $item) use ($keys_filp, $onToLocalFn) {
                    $id = $keys_filp[$key];
                    $item = call_user_func($onToLocalFn, $id, $item);
                    return $item;
                };
            }
            $cacheList = self::getMultiObj($keys, true, $onToLocalFnProxy);
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
                    $cacheKey = $keys[$id];
                    if ($expire != 0)
                    {
                        self::setObj($cacheKey, $item, $expire);
                    }
                    if ($onToLocalFn)
                    {
                        $item = call_user_func($onToLocalFn, $id, $item);
                    }
                    if ($item === false)
                    {
                        continue;
                    }
                    self::$cacheList[$cacheKey] = $item;
                    $list[$id] = $item;
                }
            }
        }

        if ($isArray)
        {
            // reset order
            $ret = array();
            foreach ($ids as $id)
            {
                if (isset($list[$id]))
                {
                    $ret[$id] = $list[$id];
                }
                else if ($default !== false)
                {
                    $ret[$id] = $default;
                }
            }
            return $ret;
        }
        else
        {
            return isset($list[$singleId]) ? $list[$singleId] : $default;
        }
    }

    public static function fetchLocal($key, $getFn)
    {
        if (!isset(self::$cacheList[$key]))
        {
            $data = call_user_func($getFn);
            self::$cacheList[$key] = $data;
        }
        return self::$cacheList[$key];
    }
}
