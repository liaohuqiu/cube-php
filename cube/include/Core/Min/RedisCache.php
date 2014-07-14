<?php
/**
 * Redis wrapper
 */
class MCore_Min_RedisCache implements MCore_Proxy_IMCache
{
    private $cache;

    public static function getInstance()
    {
        static $instance;
        if (!$instance)
        {
            $instance = new self();
        }
        return $instance;
    }

    public function __construct()
    {
        $cache = new Redis();
        $cache->pconnect('127.0.0.1', 6379);
        $this->cache = $cache;
    }

    public function set($key, $value, $expired = 0)
    {
        $value = bin_encode($value);
        if ($expired)
        {
            $ret = $this->cache->setex($key, $expired, $value);
        }
        else
        {
            $ret = $this->cache->set($key, $value);
        }
        return $ret;
    }

    public function delete($key)
    {
        return $this->cache->delete($key);
    }

    public function get($key)
    {
        $r = $this->cache->get($key);
        if ($r !== false)
        {
            $r = bin_decode($r);
        }
        return $r;
    }

    public function getMulti($keys)
    {
        $this->cache->multi(Redis::PIPELINE);
        foreach ($keys as $key)
        {
            $this->cache->get($key);
        }
        $r = $this->cache->exec();

        $list = array();
        foreach ($keys as $index => $key)
        {
            if (isset($r[$index]) && ($v = $r[$index]) !== false)
            {
                $list[$key] = bin_decode($v);
            }
        }
        return $list;
    }

    public function setObj($key, $value, $expired = 0)
    {
        return $this->set($key, $value, $expired);
    }

    public function getObj($key)
    {
        $value = $this->get($key);
        return $value;
    }

    public function getMultiObj($keys)
    {
        $values = $this->getMulti($keys);
        return $values;
    }
}
