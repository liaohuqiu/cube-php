<?php
/**
 *
 *
 * @author huqiu
 */
class MAdmin_UserRaw
{
    private static $_userInfoList = array();

    public static function create($email, $pwd)
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

        $db = MCore_Dao_DB::create();
        $update = array('pwd', 'salt', 'ctime');
        $ret = $db->insert(self::getTableKind(), $info, $update, array(), array('ctime'));
        $uid = $ret['insert_id'];
        $info['uid'] = $uid;

        return $info;
    }

    public static function getTableKind()
    {
        return MCore_Tool_Conf::getDataConfigByEnv('engine', 'admin_user_table');
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
            $map = self::getStatusMap();
            $ret['status'] = $map[$ret['status']];
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

            $map = self::getStatusMap();
            $ret['status'] = $map[$ret['status']];
            self::$_userInfoList[$uid] = $ret;
            return $ret;
        }
        else
        {
            return reset($existInfo);
        }
    }

    public static function updateInfo($uid, $info)
    {
        $db = MCore_Dao_DB::create();
        $where = array('uid' => $uid);
        $ret = $db->update(self::getTableKind(), $info, array(), $where);
    }
}
