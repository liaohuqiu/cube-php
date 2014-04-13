<?php
class MData_UserData
{
    private static $_userInfoList = array();

    public static function createOrUpdate($user_emp_id, $user_nick_name, $email, $ext = array())
    {
        $user_emp_id = intval($user_emp_id);
        $old_info = self::getInfoByEmpId($user_emp_id);
        if ($old_info)
        {
            $update = array();
            $update['user_nick_name'] = $user_nick_name;
            $update['email'] = $email;
            $update['ext'] = json_encode($ext);

            $where = array('uid' => $old_info['uid']);
            MCore_Dao_DB::create()->update('s_user_info', $update, array(), $where);
            $info = array_merge($old_info, $update);
        }
        else
        {
            $time = time();
            $salt = substr(md5($email . $time), 0, 6);

            $info = array();
            $info['email'] = $email;
            $info['user_emp_id'] = $user_emp_id;
            $info['user_nick_name'] = $user_nick_name;
            $info['salt'] = $salt;
            $info['ext'] = json_encode($ext);
            $info['ctime'] = 'now()';

            $ret = MCore_Dao_DB::create()->insert('s_user_info', $info, $update, array(), array('ctime'));
            $info['uid'] = $ret['insert_id'];
        }

        $info['ext'] = $ext;
        self::$_userInfoList[$info['uid']] = $info;

        return $info;
    }

    public static function getInfo($uid)
    {
        if (!isset(self::$_userInfoList[$uid]))
        {
            $db = MCore_Dao_DB::create();
            $where = array('uid' => $uid);
            $ret = $db->select('s_user_info', array('*'), $where)->first();
            if (!$ret)
            {
                return false;
            }
            $ret['ext'] = json_decode($ret['ext']);
            self::$_userInfoList[$uid] = $ret;
        }
        return self::$_userInfoList[$uid];
    }

    public static function getInfos($uids)
    {
        // leave empty here
    }

    public static function getInfoByEmpId($emp_id)
    {
        $where = array('user_emp_id' => $emp_id);
        $existInfo = MCore_Tool_Array::where(self::$_userInfoList, $where);
        if (empty($existInfo))
        {
            $db = MCore_Dao_DB::create();
            $ret = $db->select('s_user_info', array('*'), $where)->first();
            if (!$ret)
            {
                return false;
            }
            $ret['ext'] = json_decode($ret['ext']);
            $uid = $ret['uid'];
            self::$_userInfoList[$uid] = $ret;
            return $ret;
        }
        else
        {
            return reset($existInfo);
        }
    }
}
