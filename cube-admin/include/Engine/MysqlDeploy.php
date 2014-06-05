<?php
class MEngine_MysqlDeploy
{
    public static function getDeployData($configDataOne)
    {
        $serverSettings = $configDataOne->select('server_setting', array('*'), array('active' => 1));
        $tableSetting = $configDataOne->select('table_setting', array('*'), array());
        $kindSetting = $configDataOne->select('kind_setting', array('*'), array());

        $serverList = array();
        foreach ($serverSettings as $item)
        {
            $sid = $item['sid'];
            $master_id = $item['master_id'];
            $info = MCore_Min_TableConfig::convertServerInfoForDBResult($item);
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
            $tableInfos[$kind]['servers'][$tableIndex] = $item['sid'];
        }

        $data = array();
        $data['servers'] = $serverList;
        $data['tables'] = $tableInfos;
        return $data;
    }

    public static function createTable($configDataOne, $sids, $dbName, $kind, $idField, $tableNum, $sql)
    {
        $sidLength = count($sids);
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

        return true;
    }
}
