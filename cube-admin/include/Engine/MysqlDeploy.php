<?php
class MEngine_MysqlDeploy
{
    public static function updateDeployInfo()
    {
        $configDataOne = MEngine_EngineDB::create();
        $serverSettings = $configDataOne->select('server_setting', array('*'), array('active' => 1));
        $tableSetting = $configDataOne->select('table_setting', array('*'), array());
        $kindSetting = $configDataOne->select('kind_setting', array('*'), array());

        $serverList = array();
        foreach ($serverSettings as $item)
        {
            $sid = $item['sid'];
            $master_id = $item['master_id'];
            $info = MCore_Min_TableDeployMan::convertServerInfoForDBResult($item);
            if ($master_id)
            {
                $serverList[$master_id]['slaves'][$sid] = $info;
            }
            else
            {
                $serverList[$sid]['master'] = $info;
            }
        }

        $tableInfos = array();
        foreach ($kindSetting as $item)
        {
            $kind = $item['kind'];

            $info = array();
            $info['id_field'] = $item['id_field'];
            $info['table_num'] = $item['table_num'];
            $info['table_prefix'] = $item['table_prefix'];
            $info['app'] = $item['app'];
            $info['db_name'] = $item['db_name'];
            $tableInfos[$kind] = $info;
        }

        foreach ($tableSetting as $item)
        {
            $kind = $item['kind'];
            $tableIndex = $item['no'];

            $info = array();
            $info['no'] = $item['no'];
            $info['sid'] = $item['sid'];
            $tableInfos[$kind]['tables'][$tableIndex] = $info;
        }

        $data = array();
        $data['servers'] = $serverList;
        $data['tables'] = $tableInfos;
        MCore_Min_TableDeployMan::updateDeployData($data);
        return $data;
    }

    public static function createTable($sids, $dbName, $kind, $idField, $tableNum, $sql)
    {
        $sidLength = count($sids);
        $configDataOne = MEngine_EngineDB::create();
        $exist = $configDataOne->select('kind_setting', array('*'), array('kind' => $kind));
        if ($exist->count() > 0 )
        {
            return false;
        }
        $exist = $configDataOne->select('table_setting', array('*'), array('kind' => $kind));
        if ($exist->count() > 0 )
        {
            return false;
        }
        $info = array();
        $info['kind'] = $kind;
        $info['table_num'] = $tableNum;
        $info['table_prefix'] = $kind;
        $info['id_field'] = $idField;
        $info['db_name'] = $dbName;
        $info['app_name'] = $dbName;

        // add kind setting info
        $ret = $configDataOne->insert('kind_setting', $info);
        if (!$ret['affected_rows'])
        {
            throw new Exception('Fail to add kind_setting for: ' . $kind .  ' error: ' . var_export($ret, true));
        }

        // add table setting info(s)
        for ($i=0; $i < $tableNum; $i++)
        {
            $idx = $i % count($sids);
            $itemInfo = array();
            $itemInfo['kind'] = $kind;
            $itemInfo['no'] = $i;

            $serverIndex = $i % $sidLength;
            $itemInfo['sid'] = $sids[$serverIndex];

            $ret = $configDataOne->insert('table_setting', $itemInfo);
            if (!$ret['affected_rows'])
            {
                $msg = sprintf('Failt to add table_setting: %s $s %s', $kind, $i, $ret['error']);
                throw new Exception($msg);
            }
        }

        // create table(s)
        $iterator = new MEngine_MysqlIterator($kind, $configDataOne);
        $sql = str_replace($kind . '_0', $kind, $sql);
        try
        {
            $iterator->query($sql, null, false, false);
        }
        catch (Exception $ex)
        {
            $configDataOne->delete('kind_setting', array('kind' => $kind));
            $configDataOne->delete('table_setting', array('kind' => $kind));
            throw $ex;
        }

        // reload server
        $info = array(
            'name' => 'revision',
            'value' => 'now()',
        );
            $configDataOne->insert('variable_setting', $info, array('value'), array(), array('value'));
        return true;
    }

    /**
     * getTable info from config table
     */
    public static function queryTableInfos($kind, $useSlave = false)
    {
        $configDataOne = MEngine_EngineDB::create();
        $where = array('kind' => $kind);
        $kindInfo = $configDataOne->select('kind_setting', array('*'), $where)->first();
        $tableSettings = $configDataOne->select('table_setting', array('*'), $where);
        if (!$kindInfo || !$tableSettings)
        {
            throw new Exception('There is no config info for this: ' . $kind);
        }
        $kindInfo['tables'] = $tableSettings->toArray();

        $serverIds = MCore_Tool_Array::getFields($tableSettings['data'], 'sid');
        $serverIds = array_unique($serverIds);

        $serverIdsStr = implode(',', $serverIds);
        $where = "(sid in ($serverIdsStr) or master_sid in ($serverIdsStr)) and active = 1";
        $serverInfos = $configDataOne->selectRawWhere('server_setting', 0, array('*'), $where);

        $serverList = array();
        foreach ($serverInfos as $item)
        {
            $master_sid = $item['master_sid'];
            $sid = $item['sid'];
            $item = MCore_Min_TableDeployMan::convertServerInfoForDBResult($item);
            if(!$master_sid)
            {
                $serverList[$sid]['master'] = $item;
            }
            else
            {
                $serverList[$sid]['slaves'][] = $item;
            }
        }
        return MCore_Min_TableDeployMan::makeTableInfos($kind, $kindInfo, $serverList, $useSlave);
    }
}
