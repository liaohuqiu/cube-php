<?php
/**
 * user authorization
 *
 * @author huqiu
 */
class MAdmin_UserAuth
{
    private static $proxy;

    public static function setProxy($proxy)
    {
        self::$proxy = $proxy;
    }

    public static function getRightLinks()
    {
        if (self::$proxy)
        {
            return self::$proxy->getRightLinks();
        }
        return array();
    }

    public static function hasAuthProxy()
    {
        return self::$proxy;
    }

    public static function setLogin($uid, $remember = false)
    {
        $info = MAdmin_UserRaw::getInfo($uid);
        $salt = $info['salt'];

        $now = time();
        $token = sprintf('%s_%s_%s', $now, md5($now . $uid . $salt), $uid);

        // if not check "remember me", set expire time to 0,
        // the cookie will expire at the end of the session(close the browser);
        $time = $remember ? $now + 86400 : 0;
        $host = $_SERVER['SERVER_NAME'];
        setcookie(self::_getSessionKey(), $token, $time, '/', $host);
    }

    public static function checkLoginByGetUser()
    {
        if (self::$proxy)
        {
            return self::$proxy->checkLoginByGetUser();
        }
        $token = MCore_Tool_Input::clean('c', self::_getSessionKey(), 'str');
        list($time, $hashStr, $uid) = explode('_', $token);

        if (!$time || !$hashStr || !$uid)
        {
            return false;
        }

        $info = MAdmin_UserRaw::getInfo($uid);
        if (!$info)
        {
            return false;
        }
        if ($hashStr != md5($time . $uid . $info['salt']))
        {
            return false;
        }

        $data = array();
        $data['uid'] = $uid;
        $data['is_sysadmin'] = $info['is_sysadmin'];
        $data['name'] = $info['email'];
        $data['auth_keys'] = $info['auth_keys'];
        return new MAdmin_UserData($data);
    }

    public static function logout()
    {
        if (self::$proxy)
        {
            return self::$proxy->logout();
        }
        // just clean the cookie
        $host = $_SERVER['SERVER_NAME'];
        setcookie(self::_getSessionKey(), '', time() - 86400, '/', $host);
    }

    private static function _getSessionKey()
    {
        return '_' . APP_NAME . '_admin_token';
    }
}
