<?php
/**
 *
 *
 * @author huqiu
 */
class MAdmin_UserRaw
{
    private static $_userInfoList = array();

    public static function create($email, $pwd, $authKeys = array(), $isSysAdmin = false)
    {
        if (self::isExist($email))
        {
            return false;
        }
        $time = time();
        $salt = substr(md5($email . $time), 0, 6);
        $pwd = md5($pwd . $salt);
        $info = array();
        $info['email'] = $email;
        $info['pwd'] = $pwd;
        $info['salt'] = $salt;
        $info['ctime'] = 'now()';
        $info['is_sysadmin'] = $isSysAdmin;
        $info['auth_keys'] = implode(',', $authKeys);

        $db = MCore_Dao_DB::create();
        $update = array('pwd', 'salt', 'ctime');
        $ret = $db->insert(self::getTableKind(), $info, $update, array(), array('ctime'));
        $uid = $ret['insert_id'];
        $info['uid'] = $uid;

        $info = self::formatItem($info);
        self::$_userInfoList[$uid] = $info;
        return $info;
    }

    public static function getTableKind()
    {
        return MCore_Tool_Conf::getDataConfigByEnv('sys-config', 'admin_user_table');
    }

    private static function formatItem($item)
    {
        if (!empty($item))
        {
            $map = self::getStatusMap();
            $item['status'] = $map[$item['status']];
            $item['auth_keys'] = array_filter(explode(',', $item['auth_keys']));
        }
        return $item;
    }

    public static function getStatusMap()
    {
        return array();
    }

    public static function isExist($email)
    {
        $info = self::getInfoByEmail($email);
        return $info != false;
    }

    public static function checkUserThenGetUid($email, $pwd)
    {
        $info = self::getInfoByEmail($email);
        if (!$info)
        {
            return false;
        }
        if ($info['pwd'] == md5($pwd . $info['salt']))
        {
            return $info['uid'];
        }
        return false;
    }

    public static function getInfo($uid)
    {
        if (!isset(self::$_userInfoList[$uid]))
        {
            $db = MCore_Dao_DB::create();
            $where = array('uid' => $uid);
            $ret = $db->select(self::getTableKind(), array('*'), $where)->first();
            if (!$ret)
            {
                return false;
            }
            $ret = self::formatItem($ret);
            self::$_userInfoList[$uid] = $ret;
        }
        return self::$_userInfoList[$uid];
    }

    public static function getInfoByEmail($email)
    {
        $where = array('email' => $email);
        $existInfo = MCore_Tool_Array::where(self::$_userInfoList, $where);
        if (empty($existInfo))
        {
            $db = MCore_Dao_DB::create();
            $ret = $db->select(self::getTableKind(), array('*'), $where)->first();
            if (!$ret)
            {
                return false;
            }
            $uid = $ret['uid'];
            self::$_userInfoList[$uid] = self::formatItem($ret);
            return $ret;
        }
        else
        {
            return reset($existInfo);
        }
    }

    public static function updateInfo($uid, $info, $authKeys)
    {
        $db = MCore_Dao_DB::create();
        $where = array('uid' => $uid);
        $info['auth_keys'] = implode(',', $authKeys);
        $ret = $db->update(self::getTableKind(), $info, array(), $where);
        unset(self::$_userInfoList[$uid]);
    }

    public static function delete($uid)
    {
        MCore_Dao_DB::create()->delete(self::getTableKind(), array('uid' => $uid));
        unset(self::$_userInfoList[$uid]);
    }

    public static function checkPwdByEmail($email, $pwd)
    {
        $info = self::getInfoByEmail($email);
        if (!$info)
        {
            return false;
        }
        return self::checkPwd($info, $pwd);
    }

    public static function checkPwdById($id, $pwd)
    {
        $info = self::getInfo($id);
        if (!$info)
        {
            return false;
        }
        return self::checkPwd($info, $pwd);
    }

    public static function updatePwd($uid, $pwd)
    {
        $info = self::getInfo($uid);
        $pwd = md5($pwd . $info['salt']);

        $update = array('pwd' => $pwd);
        $where = array('uid' => $uid);
        $ret = MCore_Dao_DB::create()->update(self::getTableKind(), $update, array(), $where);
        return true;
    }

    public static function checkPwd($info, $pwd)
    {
        if ($info['pwd'] == md5($pwd . $info['salt']))
        {
            return $info;
        }
    }
}
