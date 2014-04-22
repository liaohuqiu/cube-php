<?php
/**
 * db access
 *
 * @author huqiu
 */

class MCore_Proxy_DBMan
{
    public static function create()
    {
        return new MCore_Min_DBMan();
    }
}
?>
