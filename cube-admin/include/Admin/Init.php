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
        return true;
        $sys_config_path = MEngine_SysConfig::getSysConfigPath();
        $deploy_data_path = MCore_Min_TableConfig::getConfigPath();

        if (!file_exists($sys_config_path) || !file_exists($deploy_data_path))
        {
            return false;
        }
        $sysConfig = MEngine_SysConfig::getSysConfig();
    }
}
