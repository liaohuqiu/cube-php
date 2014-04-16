<?php
class MEngine_MysqlTableCreator
{
    public static function createFromFile($serverGroupKey, $dbName, $scriptFilePath, $onlyThisTable = null)
    {
        $sqlContent = file_get_contents($scriptFilePath);
        return self::create($serverGroupKey, $dbName, $sqlContent, $onlyThisTable);
    }

    public static function create($serverGroupKey, $dbName, $sqlContent, $onlyThisTable = '')
    {
        $dataOne = MEngine_EngineDB::create();
        $where = array();
        $where['group_key'] = $serverGroupKey;
        $where['master_sid'] = 0;
        $serverList = $dataOne->select('server_setting', array('*'), $where);
        $sids = array();

        // ensure every database for tables is ready.
        foreach ($serverList as $item)
        {
            $dbInfo = MCore_Min_TableDeployMan::convertServerInfoForDBResult($item);
            $connection = MCore_Min_DBConection::get($dbInfo);
            MEngine_EngineDB::createDB($connection, $dbName);
            $sids[] = $item['sid'];
        }

        $sqlList = self::parseSqlContent($sqlContent);
        $succSqlList = array();
        foreach ($sqlList as $item)
        {
            if (!isset($item['kind']) || ! isset($item['split_id']) || !isset($item['table_num']) || !isset($item['sql']))
            {
                throw new MEngine_Exception('The format of the input sql is not right');
            }

            $kind = $item['kind'];
            if (!empty($onlyThisTable) && $onlyThisTable != $kind)
            {
                continue;
            }
            $splitId = $item['split_id'];
            $tableNum = $item['table_num'];
            $sql = $item['sql'];

            try
            {
                $ret =  MEngine_MysqlDeploy::createTable($sids, $dbName, $kind, $splitId, $tableNum, $sql);
                if ($ret)
                {
                    $succSqlList[$kind] = $sql;
                }
                else
                {
                    throw new Exception('Fail to create table: ' . $kind . '. Its config information is existent.');
                }
            }
            catch(Exception $ex)
            {
                throw $ex;
            }
        }

        MEngine_MysqlDeploy::updateDeployInfo();
        return $succSqlList;
    }

    public static function parseSqlContent($sqlContent)
    {
        $separator = '-- kind';
        $sqls = explode($separator, $sqlContent);
        $list = array();
        foreach ($sqls as $item)
        {
            if (!$item)
            {
                continue;
            }
            $sql = $separator . $item;
            $list[] = self::_parseSql($sql);
        }
        return $list;
    }

    private static function _parseSql($sqlRaw)
    {
        $list = explode("\n", $sqlRaw);
        $sqlLines = array();

        $info = array();
        foreach ($list as $item)
        {
            if (strpos($item, '-- ') !== false)
            {
                list($k, $v) = explode('=', trim(str_replace('-- ', '', $item)));
                $info[$k] = $v;
            }
            else
            {
                $sqlLines[] = $item;
            }
        }
        $info['sql'] = implode("\n", $sqlLines);
        return $info;
    }
}
