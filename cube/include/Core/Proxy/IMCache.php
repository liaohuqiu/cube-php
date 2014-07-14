<?php
interface MCore_Proxy_IMCache
{
    function set($key, $value, $expire);

    function get($key);

    function delete($key);

    function getObj($key);

    function getMulti($keys);

    function getMultiObj($keys);

    function increment($key, $value);

    function decrement($key, $value);
}
