<?php
/**
 * Wrapper for Memcached
 */
class MCore_Min_Memcached implements MCore_Proxy_IMCache
{
    private $cache;

    public function __construct($cache)
    {
        $this->cache = $cache;
    }

    public static function create()
    {
        static $cache;
        if (!$cache)
        {
            $memcached = new Memcached();
            $servers = MCore_Tool_Conf::getDataConfigByEnv('mix', 'mcache-servers');
            $memcached->addServers($servers);
            $cache = new MCore_Min_Memcached($memcached);
        }
        return $cache;
    }

    public function set($key, $value, $expire = 0)
    {
        return $this->cache->set($key, $value, $expire);
    }

    public function get($key)
    {
        return $this->cache->get($key);
    }

    public function delete($key)
    {
        return $this->cache->delete($key);
    }

    public function setObj($key, $value, $expire = 0)
    {
        if ($value === false)
        {
            return;
        }
        $value = bin_encode($value);
        return $this->cache->set($key, $value, $expire);
    }

    public function getObj($key)
    {
        $ret = $this->get($key);
        if ($ret !== false)
        {
            $ret = bin_decode($ret);
        }
        return $ret;
    }

    public function getMulti($keys)
    {
        return $this->cache->getMulti($keys);
    }

    public function getMultiObj($keys)
    {
        $r = $this->getMulti();
        $list = array();

        if (is_array($r))
        {
            foreach ($keys as $index => $key)
            {
                if (isset($r[$index]) && ($v = $r[$index]) !== false)
                {
                    $list[$key] = bin_decode($v);
                }
            }
        }
        return $list;
    }

    public function increment($key, $value = 1)
    {
        $ret = $this->cache->increment($key, $value);
        if ($ret === false)
        {
            $this->cache->set($key, $value);
            $ret = $value;
        }
        return $ret;
    }

    public function decrement($key, $value = 1)
    {
        return $this->cache->decrement($key, $value);
    }

    public function getEngine()
    {
        return $this->cache;
    }
}
