<?php
interface MCore_Proxy_IMCache
{
    function set($key, $value, $expire = 0);

    function get($key);

    function delete($key);

    function getObj($key);

    function setObj($key, $value, $expire = 0);

    function getMulti($keys);

    function getMultiObj($keys);

    function getEngine();
}
