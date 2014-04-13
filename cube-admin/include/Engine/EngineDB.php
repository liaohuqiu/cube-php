<?php
class MEngine_EngineDB
{
    public static function getEngineDataOne()
    {
        static $db;
        if (!$db)
        {
            $rawDbInfo = MCore_Tool_Conf::getDataConfigByEnv('engine', 'engine_db_server');
            $rawDbInfo['db'] = MCore_Tool_Conf::getDataConfigByEnv('engine', 'engine_db_name');
            $connection = MCore_Min_DBConection::get($rawDbInfo);

            $dbMan = new MEngine_EngineDBMan($connection);
            $db = new MCore_Dao_DB($dbMan);
        }
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
