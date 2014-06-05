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
        $sys_config_path = MEngine_SysConfig::getSysConfigPath();
        $deploy_data_path = MCore_Min_TableConfig::getConfigPath();

        if (!file_exists($sys_config_path) || !file_exists($deploy_data_path))
        {
            return false;
        }

        try
        {
            // check sysconfig
            $db = MEngine_EngineDB::fromConfig();
            $db->select('server_setting', array('count(1) as num'));

            // check db-deploy config
            $userTableName = MEngine_SysConfig::getSysConfig('admin_user_table');
            MCore_Dao_DB::create()->select($userTableName, array('count(1) as num'));
        }
        catch(Exception $ex)
        {
            return false;
        }
        return true;
    }
}
