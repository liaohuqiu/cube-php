<?php
namespace Tool;
/**
 * 缓存工具，提供常用的几个方法以及进程内缓存
 *
 * @author huqiu.lhq@taobao.com
 */
class MCore_Tool_Cache
{
    private static $proxy; // MCore_Proxy_IMCache
    private static $cacheList = array();

    private static function getCacheProxy()
    {
        if (!self::$proxy)
        {
            if (!defined(FN_getCacheProxy))
            {
                throw new MCore_Proxy_Exception('FN_getCacheProxy undefined');
            }
            $fn = FN_getCacheProxy;
            if (is_callable($fn))
            {
                self::$proxy = call_user_func($fn);
            }
        }
        return self::$proxy;
    }

    /**
     * 获取值
     */
    public static function get($key, $localCache = true)
    {
        if (!MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog('cache', 'get: ' . $key);
        }
        if ($localCache && isset(self::$cacheList[$key]))
        {
            return self::$cacheList[$key];
        }
        $ret = self::getCacheProxy()->get($key);
        // cache even the $ret is false;
        if ($localCache)
        {
            self::$cacheList[$key] = $ret;
        }
        return $ret;
    }

    /*
     * 设置值
     */
    public static function set($key, $value, $expired = 0)
    {
        if (!MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog('cache', 'set: ' . $key);
        }
        if ($expired == -1)
        {
            return self::delete($key);
        }
        unset(self::$cacheList[$key]);
        return self::getCacheProxy()->set($key, $value, $expired);
    }

    /**
     * 批量获取，返回 关联数组
     */
    public static function getMulti($keys, $localCache = true)
    {
        $list = array();
        $unHitKeys = array();
        if ($localCache)
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

        if (!empty($unHitKeys))
        {
            $cacheList = self::getCacheProxy()->getMulti($unHitKeys);
            $list = $list + $cacheList;

            if ($localCache && is_array($cacheList))
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
     * 设置数组
     */
    public static function setObj($key, $value, $expired = 0)
    {
        if (!MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog('cache', 'setObj: ' . $key);
        }
        if ($expired == -1)
        {
            return self::delete($key);
        }
        unset(self::$cacheList[$key]);
        return self::getCacheProxy()->setObj($key, $value, $expired);
    }

    /**
     * 获取数组
     */
    public static function getObj($key, $localCache = true, $onToLocalFn = null)
    {
        if ($localCache && isset(self::$cacheList[$key]))
        {
            return self::$cacheList[$key];
        }
        if (!MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog('cache', 'getObj: ' . $key);
        }
        $ret = self::getCacheProxy()->getObj($key);
        if ($ret !== false && $localCache)
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
     * 批量获取数组
     */
    public static function getMultiObj($keys, $localCache = true, $onToLocalFn = null)
    {
        if (!MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog('cache', 'getMultiObj: ' . implode(',', $keys));
        }
        $list = array();
        $unHitKeys = array();
        if ($localCache)
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

        if (!empty($unHitKeys))
        {
            $cacheList = self::getCacheProxy()->getMultiObj($unHitKeys);
            if (is_array($cacheList) && !empty($cacheList))
            {
                foreach ($cacheList as $key => $item)
                {
                    if ($localCache)
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

    public static function delete($key)
    {
        if (!MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog('cache', 'delete: ' . $key);
        }
        unset(self::$cacheList[$key]);
        return self::getCacheProxy()->delete($key);
    }

    public static function fetch($cacheKey, $getFn, $onToLocalFn = null, $expired = 0)
    {
        $data = false;
        if ($expired >= 0)
        {
            $data = \Tool\Cache::getObj($cacheKey, true, $onToLocalFn);
        }
        if ($data === false)
        {
            $data = call_user_func($getFn);
            if ($data !== false || $expired < 0)
            {
                \Tool\Cache::setObj($cacheKey, $data, $expired);
            }
            if ($onToLocalFn)
            {
                $data = call_user_func($onToLocalFn, $data);
                self::$cacheList[$key] = $data;
            }
        }
        return $data;
    }

    /**
     * 获取ids对应的列表缓存
     *
     * onToLocalFn($id, $item)
     */
    public static function fetchList($ids, $getKeyFn, $getListFn, $onToLocalFn = null, $expired = 0, $default = false)
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
                var_export($ids);
                throw new \Exception();
            }
            $keys[$id] = call_user_func($getKeyFn, $id);
        }

        $cacheList = array();
        if ($expired >= 0)
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
                    self::setObj($cacheKey, $item, $expired);
                    if ($onToLocalFn)
                    {
                        $item = call_user_func($onToLocalFn, $id, $item);
                        if ($item === false)
                        {
                            continue;
                        }
                        self::$cacheList[$cacheKey] = $item;
                    }
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
