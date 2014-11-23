<?php
/**
 * 用户的keyval
 *
 * @author huqiu
 */
class MCore_Tool_UserKv
{
    private $table;

    function __construct()
    {
        $this->table = MCore_Tool_Conf::getDataConfigByEnv('mix', 'userkv_table');
    }

    public function set($uid, $key, $value)
    {
        $orginInfo = array(
            'uid'   =>      $uid,
            'k'     =>      $key,
            'v'     =>      $value,
        );
        $insertField = $orginInfo;
        $insertField['v'] = serialize($value);

        $ret = MCore_Dao_DB::create()->insert($this->table, $insertField, array('v'));

        return true;
    }

    public function get($uid, $key)
    {
        $info = $this->getRaw($uid, $key);
        if($info === false)
        {
            return false;
        }
        return $info['v'];
    }

    public function getRaw($uid, $key)
    {
        $dbArr = $this->_getRawDataFromDb($uid, $key);
        return $dbArr;
    }

    private function _getRawDataFromDb($uid, $key)
    {
        $selectField = array('uid', 'v', 'mtime');
        $where = array(
            'uid' => $uid,
            'k' => $key
        );
        $ret = MCore_Dao_DB::create()->select($this->table, $selectField, $where);
        if($ret['row_num']>0)
        {
            $data = $ret['data'][0];
            $data['v'] = unserialize($data['v']);
        }
        else
        {
            return false;
        }
        return $data;
    }

    /**
     * 删除值
     */
    public function delete($uid, $key)
    {
        $whereField = array(
            'uid' => $uid,
            'k' => $key
        );
        MCore_Dao_DB::create()->delete($this->table, $whereField);
    }
}
