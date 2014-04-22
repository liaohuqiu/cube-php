<?php
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

    public function set($key, $value, $expire)
    {
        return $this->cache->set($key, $value, $expire);
    }

    public function add($key, $value, $expire)
    {
        return $this->cache->add($key, $value, $expire);
    }

    public function get($key)
    {
        return $this->cache->get($key);
    }

    public function getMulti($keys)
    {
        return $this->cache->getMulti($keys);
    }

    public function delete($key)
    {
        return $this->cache->delete($key);
    }

    public function increment($key, $value)
    {
        return $this->cache->increment($key, $value);
    }

    public function decrement($key, $value)
    {
        return $this->cache->decrement($key, $value);
    }
}
