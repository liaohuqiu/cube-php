<?php
/**
 * create config table
 *
 * @author srain
 */
include dirname(dirname(dirname(__FILE__))) . '/cube-admin-boot.php';
class App extends MCore_Cli_ConsoleBase
{
    function main()
    {
        $rawServerInfo = MCore_Tool_Conf::getDataConfigByEnv('engine', 'engine_db_server');
        $engineDBName = MCore_Tool_Conf::getDataConfigByEnv('engine', 'engine_db_name');

        $dbInfo = $rawServerInfo;
        $dbInfo['db'] = '';
        $connection = MCore_Min_DBConection::get($dbInfo);

        // create table config information database;
        MEngine_EngineDB::createDB($connection, $engineDBName);
        $connection->selectDB($engineDBName);

        // create config tables
        $separator = 'CREATE TABLE';
        $sqlText = file_get_contents('dev_config.sql');
        $sqls = explode($separator, $sqlText);
        foreach ($sqls as $item)
        {
            if (!$item)
            {
                continue;
            }
            $sql = $separator . $item;
            $this->printInfo($sql, false);
            $ret = $connection->query($sql);
        }

        // add default server info
        $dataOne = MEngine_EngineDB::getEngineDataOne();
        $serverList = $dataOne->select('server_setting', array('*'), array());

        $configDBServerGroupKey = $rawServerInfo['server_group_key'];
        $where = array('group_key' => $configDBServerGroupKey);
        if ($serverList->where($where)->count() == 0)
        {
            $info = array();
            $info['host'] = $rawServerInfo['h'];
            $info['port'] = $rawServerInfo['P'];
            $info['group_key'] = $configDBServerGroupKey;
            $info['user'] = $rawServerInfo['u'];
            $info['passwd'] = $rawServerInfo['p'];
            $ret = $dataOne->insert('server_setting', $info, array());
        }

        // create table
        $adminUserTable = MCore_Tool_Conf::getDataConfigByEnv('engine', 'admin_user_table');
        $sqlContent = file_get_contents('admin.sql');
        $sqlContent = str_replace('{admin_user_table}', $adminUserTable, $sqlContent);

        $adminTableDBName = MCore_Tool_Conf::getDataConfigByEnv('engine', 'admin_table_db_name');
        MEngine_MysqlTableCreator::create($configDBServerGroupKey, $adminTableDBName, $sqlContent);

        // init value
        $admin = MCore_Tool_Conf::getDataConfigByEnv('engine', 'admin_init_user_name');
        $pwd = MCore_Tool_Conf::getDataConfigByEnv('engine', 'admin_init_user_pwd');
        MAdmin_UserRaw::create($admin, $pwd);
    }
}
$app = new App();
$app->run();
