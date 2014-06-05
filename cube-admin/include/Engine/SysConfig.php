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
        return MCore_Tool_Conf::getDataConfigByEnv('sys-config', func_get_args());
    }

    public static function buildSysConfig($dbInfo, $userTableName)
    {
        $sysConfig = array();
        $sysConfig['sys_config_db'] = $dbInfo;
        $sysConfig['admin_user_table'] = $userTableName;
        return $sysConfig;
    }
}
