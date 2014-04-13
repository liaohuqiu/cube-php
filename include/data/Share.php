<?php
class MData_Share
{
    public static function addShare($uid, $title, $begin_time, $place, $data = array())
    {
        $data = array();
        $data['uid'] = $uid;
        $data['title'] = $title;
        $data['place'] = $place;
        $data['begin_time'] = $begin_time;
        $data['join_num'] = 0;
        $data['ext'] = json_encode($data);
        $data['ctime'] = 'now()';

        $db = new MCore_Dao_DB();
        $ret = $db->insert('s_share_event', $data, array(), array(), array('ctime'));
        $data['sid'] = $ret['insert_id'];
        return $data;
    }

    public static function getShareInfo($share_id)
    {
        return MCore_Dao_DB::create()->select('s_share_event', '*', array('sid' => $share_id))->first();
    }

    public static function updateShareInfo($uid, $share_id, $title, $begin_time, $place)
    {
        $data = array();
        $data['uid'] = $uid;
        $data['title'] = $title;
        $data['place'] = $place;
        $data['begin_time'] = $begin_time;

        $where = array('sid' => $share_id);
        $ret = MCore_Dao_DB::create()->update('s_share_event', $data, array(), $where);

        $data['sid'] = $share_id;
        return $data;
    }

    public static function getList()
    {
        $db = new MCore_Dao_DB();
        $where = '';
        $result = $db->selectRawWhere('s_share_event', 0, '*', $where);
        return $result->toArray();
    }

    public static function getUserList($uid)
    {
        return MCore_Dao_DB::create()->select('s_share_event', '*', array('uid' => $uid))->toArray();
    }

    public static function toDisplayItem($uid, $data)
    {
        $user_join_list = self::getJoinList($uid);
        $ids = MCore_Tool_Array::getFields($user_join_list, 'sid');
        $begin_time = strtotime($data['begin_time']);
        $is_past = $begin_time < time();
        $host_user_info = MData_UserData::getInfo($data['uid']);

        $info = array();
        $info['uid'] = $data['uid'];
        $info['sid'] = $data['sid'];
        $info['creator_user_name'] = !empty($host_user_info) ? $host_user_info['user_nick_name'] : '暂无';
        $info['begin_time'] = date('Y-m-d', $begin_time);
        $info['title'] = $data['title'];
        $info['join_num'] = $data['join_num'] ? $data['join_num'] : 0;
        $info['place'] = $data['place'];

        $info['can_join'] = $data['uid'] != $uid && !$is_past;
        $info['can_edit'] = $data['uid'] == $uid && !$is_past;
        $info['joined'] = in_array($data['sid'], $ids);
        return $info;
    }

    public static function deleteShare($uid, $share_id)
    {
        $where = array('uid' => $uid, 'sid' => $share_id);
        MCore_Dao_DB::create()->delete('s_share_event', $where);
    }

    public static function getJoinList($uid)
    {
        static $list = array();
        if (!isset($list[$uid]))
        {
            $list[$uid] = MCore_Dao_DB::create()->select('s_share_event_join', '*', array('uid' => $uid))->toArray();
        }
        return $list[$uid];
    }

    public static function joinShare($uid, $share_id)
    {
        $info = array();
        $info['uid'] = $uid;
        $info['sid'] = $share_id;
        $info['is_cancle'] = 0;
        $ret = MCore_Dao_DB::create()->insert('s_share_event_join', $info, array('is_cancle'));

        if ($ret['affected_rows'])
        {
            MCore_Dao_DB::create()->update('s_share_event', array(), array('join_num' => 1), array('sid' => $share_id));
        }
    }

    public static function cancelJoinShare($uid, $share_id)
    {
        $where = array('uid' => $uid, 'sid' => $share_id);
        $ret = MCore_Dao_DB::create()->delete('s_share_event_join', $where);
        if ($ret['affected_rows'])
        {
            MCore_Dao_DB::create()->update('s_share_event', array(), array('join_num' => -1), array('sid' => $share_id));
        }
    }
}
