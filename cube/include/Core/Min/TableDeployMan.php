<?php
/**
 * @author      huqiu
 */
class MCore_Min_TableDeployMan
{
    public static function updateDeployData($data)
    {
        MCore_Tool_Conf::updateDataConfigByEnv('auto-gen-db-deploy', $data);
    }

    public static function getDeployData()
    {
        return MCore_Tool_Conf::getDataConfigByEnv('auto-gen-db-deploy');
    }

    public static function getDeployInfo($kind)
    {
        $data = self::getDeployData();
        return $data['tables'][$kind];
    }

    public static function getIdField($kind)
    {
        $data = self::getDeployData();
        return $data['tables'][$kind]['id_field'];
    }

    public static function getTableNum($kind)
    {
        $data = self::getDeployData();
        return $data['tables'][$kind]['table_num'];
    }

    public static function getDBInfo($kind, $hintId, $useSlave = false)
    {
        $data = self::getDeployData();
        $kindInfo = $data['tables'][$kind];
        if (!$kindInfo)
        {
            throw new Exception('table deploy info is empty, kind: ' . $kind);
        }

        $tableIndex = $hintId % $kindInfo['table_num'];
        $tableInfo = $kindInfo['tables'][$tableIndex];
        $sid = $tableInfo['sid'];
        $server = $data['servers'][$sid];

        $serverInfo = array();
        if ($useSlave && isset($server['slaves']) && $server['slaves'])
        {
            $serverInfo = Core_Tool_Array::rand($server['slaves']);
        }
        else
        {
            $serverInfo = $server['master'];
        }
        $serverInfo['db'] = $kindInfo['db_name'];
        return MCore_Min_DBInfo::create($serverInfo);
    }

    public static function makeTableInfos($kind, $kindInfo, $serverList, $salve)
    {
        if (empty($kindInfo))
        {
            throw new MCore_Min_DBException('$kindInfo is empty');
        }

        $list = array();
        $isSplit = $kindInfo['table_num'];
        $tablePrefix = $kindInfo['table_prefix'];
        $dbName = $kindInfo['db_name'];

        foreach ($kindInfo['tables'] as $item)
        {
            $sid = $item['sid'];
            $tableIndex = $item['no'];

            $serverInfoRaw = $serverList[$sid];

            $tableName = $tablePrefix;
            if ($isSplit > 1)
            {
                $tableName .= '_' . $tableIndex;
            }
            if($salve && $serverInfoRaw['slaves'])
            {
                $serverInfo = Core_Tool_Array::rand($serverInfoRaw['slaves']);
            }
            else
            {
                $serverInfo = $serverInfoRaw['master'];
            }

            $dbInfoArr = $serverInfo;
            $dbInfoArr['db'] = $dbName;

            $dbInfo = MCore_Min_DBInfo::create($dbInfoArr);

            $tableInfo = new MCore_Min_TableInfo($kind, $tableIndex, $tableName, $sid, $dbInfo);
            $list[$tableIndex] = $tableInfo;
        }
        return $list;
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
        return $info;
    }

    /**
     * getTableInfo from config file
     */
    public function getTableInfos($kind, $useSlave)
    {
        $data = self::getDeployData();

        $serverList = $data['servers'];
        $tableInfo = $data['tables'][$kind];
        if (!$tableInfo)
        {
            throw new Exception('There is no config info for this table: ' . $kind);
        }
        return self::makeTableInfos($kind, $tableInfo, $serverList, $useSlave);
    }
}
