<?php
/**
 *  MCore_Proxy_MCache
 *
 *  Never should store `false` value, false is used to mark a value is not set.
 *
 * @author
 */
class MCore_Proxy_MCache
{
    private $mcache;   //IMCache
    private $prefix = '';
    private $prefix_len = 0;
    private $instance;

    public function __construct($mcache = null)
    {
        if (!$mcache)
        {
            $mcache = MCore_Min_Memcached::create();
        }

        $this->prefix = MCACHE_KEY_PRE;
        $this->prefix_len = strlen($this->prefix);
        $this->mcache = $mcache;
    }

    public static function init($mcache, $key)
    {
        self::$instance = new MCore_Proxy_MCache($mcache, $key);
    }

    public static function getInstance()
    {
        return $instance;
    }

    public function set($key, $value, $expire = 0)
    {
        $this->prefix_len && $key = $this->prefix . $key;
        return $this->mcache->set($key, $value, $expire);
    }

    public function add($key, $value, $expire = 0)
    {
        $this->prefix_len && $key = $this->prefix . $key;
        return $this->mcache->add($key, $value, $expire);
    }

    public function setObj($key, $value, $expire = 0)
    {
        $this->set($key, bin_encode($value), $expire);
    }

    public function get($key)
    {
        $this->prefix_len && $key = $this->prefix . $key;
        return $this->mcache->get($key);
    }

    public function getObj($key)
    {
        $value = $this->get($key);
        return bin_decode($value);
    }

    public function getMulti($keys)
    {
        if (empty($keys))
        {
            return array();
        }

        $tmpKeys = array_map(strval, array_unique($keys));
        $keys = array();
        foreach ($tmpKeys as $key)
        {
            $this->prefix_len && $key = $this->prefix . $key;
            $keys[] = $key;
        }
        $len = count($keys);
        $tmpRet = array();
        for ($i = 0; $i < $len; $i += 500)
        {
            $subkey = array_slice($keys, $i, 500);
            $ret = $this->mcache->getMulti($subkey);
            $tmpRet = array_merge($tmpRet, $ret);
        }

        $ret = array();
        foreach ($tmpRet as $k => $v)
        {
            $this->prefix_len && $k = substr($k, $this->prefix_len);
            $ret[$k] = $v;
        }
        return $ret;
    }

    public function getMultiObj($keys)
    {
        $values = $this->getMulti($keys);
        foreach ($values as $key => $value)
        {
            $values[$key] = bin_decode($value);
        }
        return $values;
    }

    public function delete($key)
    {
        $this->prefix_len && $key = $this->prefix . $key;
        return $this->mcache->delete($key);
    }

    /**
     * if the key is not exsit return false
     */
    function increment($key, $value)
    {
        $this->prefix_len && $key = $this->prefix . $key;
        return $this->mcache->increment($key,$value);
    }

    /**
     * create if not exist
     */
    function incrementEx($key, $value = 1, $expire = 0)
    {
        $ret = $this->increment($key, $value);
        if ($ret !== false)
        {
            return $ret;
        }
        $ret = $this->add($key, $value, $expire);
        if ($ret !== false)
        {
            return $value;
        }
        return $this->increment($key, $value);
    }

    public function decrement($key , $value)
    {
        $this->prefix_len && $key = $this->prefix . $key;
        return $this->mcache->decrement($key,$value);
    }
}
