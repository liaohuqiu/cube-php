<?php
class MEngine_MysqlDeploy
{
    public static function getDeployData($db = null)
    {
        if ($db == null)
        {
            $db = MEngine_EngineDB::fromConfig();
        }

        $raw_db_list = $db->select('sys_db_info', array('*'), array('active' => 1));
        $raw_table_list = $db->select('sys_table_info', array('*'), array());

        $db_list = array();
        foreach ($raw_db_list as $item)
        {
            $sid = (int)$item['sid'];
            $info = MCore_Min_TableConfig::convertServerInfoForDBResult($item);
            $db_list[$sid] = $info;
        }

        $db_group = array();
        foreach ($raw_db_list as $item)
        {
            $sid = (int)$item['sid'];
            $group_key = $item['group_key'];
            $master_id = (int)$item['master_id'];
            $cluster_index = (int)$item['cluster_index'];
            if ($master_id)
            {
                $cluster_index = $db_list[$master_id]['cluster_index'];
                $db_map[$group_key][$cluster_index]['slaves'][$sid] = $sid;
            }
            else
            {
                $db_map[$group_key][$cluster_index]['master'] = $sid;
            }
        }

        $table_list = array();
        foreach ($raw_table_list as $item)
        {
            $name = $item['name'];

            $info = array();
            $info['id_field'] = $item['id_field'];
            $info['table_num'] = (int)$item['table_num'];
            $info['db_group'] = $item['db_group'];
            $table_list[$name] = $info;
        }

        $data = array();
        $data['db_list'] = $db_list;
        $data['db_map'] = $db_map;
        $data['tables'] = $table_list;
        return $data;
    }

    public static function createTable($db, $name, $db_group, $idField, $tableNum, $sql, $onlyScheme = false)
    {
        $exist = $db->select('sys_table_info', array('*'), array('name' => $name));
        if ($exist->count() > 0 )
        {
            return false;
        }
        $info = array();
        $info['name'] = $name;
        $info['table_num'] = $tableNum;
        $info['id_field'] = $idField;
        $info['db_group'] = $db_group;

        $ret = $db->insert('sys_table_info', $info);
        if (!$ret['affected_rows'])
        {
            throw new Exception('Fail to add sys_table_info for: ' . $name .  ' error: ' . var_export($ret, true));
        }

        // create table(s)
        if (!$onlyScheme)
        {
            $iterator = new MEngine_MysqlIterator($name, $db);
            $sql = str_replace($name . '_0', $name, $sql);
            try
            {
                $iterator->query($sql, null, false, false);
            }
            catch (Exception $ex)
            {
                $db->delete('sys_table_info', array('name' => $name));
                throw $ex;
            }
        }

        return true;
    }
}
