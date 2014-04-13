<?php
/**
 *
 *
 * @author huqiu
 */
class MUser_UserManager extends MCore_Util_ArrayLike
{
    public static function tryGetUser()
    {
        $user = self::getUser();
        if ($user)
        {
            return $user;
        }
        $back_url = $_SERVER['SCRIPT_URI'];
        $sso = new MAli_SSO();
        $sso->login($back_url);
    }

    public static function onLoginSuccCallback($buc_token, $back_url)
    {
        $sso = new MAli_SSO();
        $info = $sso->getUserInfo($buc_token);
        $buc_info = $info['content'];
        if ($buc_info)
        {
            $user_info = MData_UserData::createOrUpdate($buc_info['empId'], $buc_info['nickNameCn'], $buc_info['emailAddr'], $buc_info);
            self::setLogin($user_info['uid'], false);
            header('Location: ' . $back_url);
            exit;
        }
    }

    public static function setLogin($uid, $remember = false)
    {
        $info = MData_UserData::getInfo($uid);
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

        $info = MData_UserData::getInfo($uid);
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
        return '_' . APP_NAME . '_token';
    }
}
