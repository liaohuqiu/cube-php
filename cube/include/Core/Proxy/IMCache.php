<?php
interface MCore_Proxy_IMCache
{
    function set($key, $value, $expire);

    function add($key, $value, $expire);

    function get($key);

    function getMulti($keys);

    function delete($key);

    function increment($key, $value);

    function decrement($key, $value);
}
