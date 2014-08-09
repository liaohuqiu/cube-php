<?php
class MEngine_EngineDB
{
    public static function fromConfig()
    {
        static $db;
        if (!$db)
        {
            $rawDbInfo = MEngine_SysConfig::getSysConfig('sys_config_db');
            $db = self::fromDBInfo($rawDbInfo);
        }
        return $db;
    }

    public static function fromDBInfo($dbInfo)
    {
        $connection = MCore_Min_DBConection::get($dbInfo);

        $dbMan = new MEngine_EngineDBMan($connection);
        $db = new MCore_Dao_DB($dbMan);
        return $db;
    }

    public static function createDB($connection, $dbName)
    {
        $databaseList = $connection->query('show databases');
        if ($databaseList->where(array('Database' => $dbName))->count() == 0)
        {
            $sql = "create database $dbName";
            $connection->query($sql);
        }
    }
}
