<?php
/**
 *
 *
 * @author huqiu
 */
class MAdmin_UserAuth
{
    public static function setLogin($uid, $remember = false)
    {
        $info = MAdmin_UserRaw::getInfo($uid);
        $salt = $info['salt'];

        $now = time();
        $token = sprintf('%s_%s_%s', $now, md5($now . $uid . $salt), $uid);

        // if not check "remember me", set expire time to 0,
        // the cookie will expire at the end of the session(close the browser);
        $time = $remember ? $now + 86400 : 0;
        $host = $_SERVER['HTTP_HOST'];
        setcookie(self::_getSessionKey(), $token, $time, '/', $host);
    }

    public static function getUser()
    {
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
        return $info;
    }

    public static function logout()
    {
        // just clean the cookie
        $host = $_SERVER['HTTP_HOST'];
        setcookie(self::_getSessionKey(), '', time() - 86400, '/', $host);
    }

    private static function _getSessionKey()
    {
        return '_' . APP_NAME . '_admin_token';
    }
}
