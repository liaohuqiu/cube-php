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
        $sys_config_path = MEngine_EngineDB::getSysConfigPath();
        $deploy_data_path = MCore_Min_TableConfig::getConfigPath();

        if (!file_exists($sys_config_path) || !file_exists($deploy_data_path))
        {
        }
        $sysConfig = MEngine_SysConfig::getSysConfig();
    }
}
