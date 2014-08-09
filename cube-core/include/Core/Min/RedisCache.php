<?php
/**
 * Redis wrapper
 *
 * The value cache into Redis will be convert into String.
 */
class MCore_Min_RedisCache implements MCore_Proxy_IMCache
{
    private $cache;

    public static function create()
    {
        static $instance;
        if (!$instance)
        {
            $instance = new self();
        }
        return $instance;
    }

    public function __construct($host = '127.0.0.1', $port = 6379)
    {
        $cache = new Redis();
        $cache->pconnect($host, $port);
        $this->cache = $cache;
    }

    public function set($key, $value, $expired = 0)
    {
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
                $list[$key] = $v;
            }
        }
        return $list;
    }

    public function setObj($key, $value, $expired = 0)
    {
        if ($value === false)
        {
            return;
        }
        $value = bin_encode($value);
        return $this->set($key, $value, $expired);
    }

    public function getObj($key)
    {
        $r = $this->get($key);
        if ($r !== false)
        {
            $r = bin_decode($r);
        }
        return $r;
    }

    public function getMultiObj($keys)
    {
        $values = $this->getMulti($keys);
        foreach ($values as $key => $r)
        {
            $values[$key] = bin_decode($r);
        }
        return $values;
    }

    public function increment($key, $value = 1)
    {
        if ($value != 1)
        {
            return $this->cache->incrBy($key, $value);
        }
        else
        {
            return $this->cache->incr($key);
        }
    }

    public function decrement($key, $value = 1)
    {
        if ($value != 1)
        {
            return $this->cache->decrBy($key, $value);
        }
        else
        {
            return $this->cache->decr($key);
        }
    }

    public function getEngine()
    {
        return $this->cache;
    }
}
