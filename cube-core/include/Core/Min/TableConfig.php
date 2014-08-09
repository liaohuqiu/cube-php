<?php
/**
 * @author      huqiu
 */
class MCore_Min_TableConfig
{
    public static function getDeployData()
    {
        return MCore_Tool_Conf::getDataConfigByEnv('db-deploy');
    }

    public static function setDeployData($data)
    {
        MCore_Tool_Conf::setDataConfig('db-deploy', $data, true);
    }

    public static function getConfigPath()
    {
        return MCore_Tool_Conf::getDataConfigPathByEnv('db-deploy');
    }

    public static function getDeployInfo($table_name)
    {
        $data = self::getDeployData();
        return $data['tables'][$table_name];
    }

    public static function getIdField($table_name)
    {
        $data = self::getDeployData();
        return $data['tables'][$table_name]['id_field'];
    }

    public static function getTableNum($table_name)
    {
        $data = self::getDeployData();
        return $data['tables'][$table_name]['table_num'];
    }

    public static function getDBInfo($table_name, $hint_id, $slave = false)
    {
        $deploy_data = self::getDeployData();
        $table_info = $deploy_data['tables'][$table_name];
        if (!$table_info)
        {
            throw new Exception('table deploy info is empty, table_name: ' . $table_name);
        }

        $db_group = $table_info['db_group'];

        if (!isset($deploy_data['db_map'][$db_group]))
        {
            throw new Exception('db map not found in config');
        }
        $cluster_list = $deploy_data['db_map'][$db_group];

        $cluster_index = ($hint_id % $table_info['table_num']) % count($cluster_list);
        $cluster = $cluster_list[$cluster_index];

        if ($slave && isset($cluster['slaves']) && $cluster['slaves'])
        {
            $sid = Core_Tool_Array::rand($cluster['slaves']);
        }
        else
        {
            $sid = $cluster['master'];
        }
        $db_info = $deploy_data['db_list'][$sid];
        return MCore_Min_DBInfo::create($db_info);
    }

    public static function convertServerInfoForDBResult($item)
    {
        $port = $item['port'];
        !$port && $port = 3306;
        $info = array();
        $info['h'] = $item['host'];
        $info['P'] = $port;
        $info['u'] = $item['user'];
        $info['p'] = $item['passwd'];
        $info['db'] = $item['db_name'];
        $info['charset'] = $item['charset'];
        return $info;
    }

    public static function getTableInfos($table_name, $salve)
    {
        $deploy_data = self::getDeployData();
        $table_info = $deploy_data['tables'][$table_name];
        if (!$table_info)
        {
            throw new Exception('There is no config info for this table: ' . $table_name);
        }
        $list = array();

        $table_num = $table_info['table_num'];
        $is_multi = $table_num > 1;
        for ($i = 0; $i < $table_num; $i++)
        {
            $name = $table_name;
            if ($is_multi)
            {
                $name = $table_name . '_' . $i;
            }

            $info = array();
            $info['db_info'] = self::getDBInfo($table_name, $i, $salve);
            $info['table_name'] = $name;
            $list[$i] = $info;
        }
        return $list;
    }
}
