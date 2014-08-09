<?php
class MEngine_MysqlTableCreator
{
    private $db;

    public function __construct($db = null)
    {
        if ($db == null)
        {
            $db = MEngine_EngineDB::fromConfig();
        }
        $this->db = $db;
    }

    public function createFromFile($serverGroupKey, $dbName, $scriptFilePath, $onlyThisTable = null)
    {
        $sqlContent = file_get_contents($scriptFilePath);
        return $this->create($serverGroupKey, $dbName, $sqlContent, $onlyThisTable);
    }

    public function createTable($serverGroupKey, $sqlContent, $onlyThisTable = '', $onlyScheme = false)
    {
        $sqlList = self::parseSqlContent($sqlContent);
        $succSqlList = array();
        foreach ($sqlList as $item)
        {
            if (!isset($item['kind']) || ! isset($item['split_id']) || !isset($item['table_num']) || !isset($item['sql']))
            {
                throw new Exception('The format of the input sql is not right');
            }

            $kind = $item['kind'];
            if (!empty($onlyThisTable) && $onlyThisTable != $kind)
            {
                continue;
            }
            $splitId = $item['split_id'];
            $tableNum = $item['table_num'];
            $sql = $item['sql'];

            $ret =  MEngine_MysqlDeploy::createTable($this->db, $kind, $serverGroupKey, $splitId, $tableNum, $sql, $onlyScheme);
            if ($ret)
            {
                $succSqlList[$kind] = $sql;
            }
        }
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
        $info['sql'] = $sqlRaw;
        return $info;
    }
}
