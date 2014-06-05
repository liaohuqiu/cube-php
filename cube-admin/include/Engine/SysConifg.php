<?php
/**
 * System config
 */
class MEngine_SysConfig
{
    public static function getSysConfigPath()
    {
        return MCore_Tool_Conf::getDataConfigPathByEnv('sys-config');
    }

    public static function updateSysConfig($data)
    {
        MCore_Tool_Conf::setDataConfig('sys-config', $data, true);
    }

    public static function getSysConfig()
    {
        return MCore_Tool_Conf::getDataConfigByEnv('sys-config', 'sys_config_db');
    }
}
