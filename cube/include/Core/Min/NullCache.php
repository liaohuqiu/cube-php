<?php
class MCore_Min_NullCache implements MCore_Proxy_IMCache
{
    public static function create()
    {
        return new MCore_Min_NullCache();
    }

    function set($key, $value, $expire)
    {
        return false;
    }

    function get($key)
    {
        return false;
    }

    function delete($key)
    {
        return false;
    }

    function getObj($key)
    {
        return false;
    }

    function setObj($key, $value, $expire)
    {
        return false;
    }

    function getMulti($keys)
    {
        return false;
    }

    function getMultiObj($keys)
    {
        return false;
    }

    function increment($key, $value)
    {
        return false;
    }

    function decrement($key, $value)
    {
        return false;
    }
}
