<?php
/**
 *
 *
 * @author huqiu
 */
class MAdmin_Init
{
    public static function checkInit()
    {
        try
        {
            self::checkInitException();
        }
        catch(Exception $ex)
        {
            return false;
        }
        return true;
    }

    public static function checkInitException()
    {
        $sys_config_path = MEngine_SysConfig::getSysConfigPath();
        $deploy_data_path = MCore_Min_TableConfig::getConfigPath();

        if (!file_exists($sys_config_path))
        {
            throw new Exception('config file is not existent: ' . $sys_config_path);
        }
        if (!file_exists($deploy_data_path))
        {
            throw new Exception('config file is not existent: ' . $deploy_data_path);
        }

        // check sysconfig
        $db = MEngine_EngineDB::fromConfig();
        $db->select('sys_db_info', array('count(1) as num'));

        // check db-deploy config
        $userTableName = MEngine_SysConfig::getSysConfig('admin_user_table');
        MCore_Dao_DB::create()->select($userTableName, array('count(1) as num'));
    }
}
