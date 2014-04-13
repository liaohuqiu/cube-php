<?php
/**
 * 用户的keyval
 *
 * @author huqiu
 */
class MCore_Tool_UserKv
{
    private $table;
    private static $useCache = false;

    function __construct()
    {
        $data = MCore_Tool_Conf::getDataConfigByEnv('base');
        $this->table = $data['userkv_table'];
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
        $ret = $this->mcore_data_split->insert($this->table, $uid, $insertField,array('v'));

        if (self::$useCache)
        {
            $mcKey = $this->_getMCKey($uid, $key);
            $this->mcore_mid_mCache->delete($mcKey);
        }
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
        if (self::$useCache)
        {
            $mcKey = $this->_getMCKey($uid, $key);

            $cache = $this->mcore_mid_mCache->getObj($mcKey);
            if(false !== $cache)
            {
                return $cache;
            }
        }

        $dbArr = $this->_getRawDataFromDb($uid, $key);
        if ($dbArr && self::$useCache)
        {
            $this->mcore_mid_mCache->setObj($mcKey, $dbArr, 86400);
        }
        return $dbArr;
    }

    private function _getRawDataFromDb($uid, $key)
    {
        $selectField = array('uid', 'v', 'mtime');
        $where = array('k' => $key);
        $ret = $this->mcore_data_split->select($this->table, $uid, $selectField, $where);
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
        $whereField = array('k'=>$key);
        $ret = $this->mcore_data_split->delete($this->table, $uid, $whereField);

        if (self::$useCache)
        {
            $mcKey = $this->_getMCKey($uid, $key);
            $this->mcore_mid_mCache->delete($mcKey);
        }
    }

    private function _getMCKey($uid, $key)
    {
        return sprintf('kv_%s_%s', $uid, $key);
    }
}
?>
