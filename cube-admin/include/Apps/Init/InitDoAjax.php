<?php
class MApps_Init_InitDoAjax extends MApps_AdminAjaxBase
{
    private $dbInfo;
    private $connection;
    private $input;

    protected function checkAuth()
    {
    }

    protected function main()
    {
        $data = $this->request->getData('data', 'r', 'json');
        $cmd = $this->request->getData('cmd');

        if ($cmd == 'check-deploy')
        {
            $this->checkDeploy();
            return;
        }

        $this->dbInfo = $this->formatDBInfo($data);
        $this->input = $data;
        if (!$this->checkConnection())
        {
            return;
        }

        if ($cmd == 'deploy')
        {
            $this->doDeploy();
        }
        else if ($cmd == 'get-config')
        {
            $this->getConfigInfo();
        }
        else if ($cmd == 'reset')
        {
            $this->clearSetting();
        }
    }

    private function doDeploy()
    {
        $dbInfo = $this->dbInfo;
        $sysDBName = $dbInfo['db'];
        $userDBName = $this->input['user_db'];
        $userPwd = $this->input['user_pwd'];
        $adminUserDB = $this->input['user_db'];
        $adminUserTable = $this->input['user_table'];
        $sysDBKey = $this->input['db_key'];

        // check input
        if (!$userPwd)
        {
            $this->setData('error_keys', array('user_pwd'));
            $this->setError('System admin user password is emtpy');
            return;
        }

        // create system tables
        $this->connection->selectDB($sysDBName);
        $separator = 'CREATE TABLE';
        $sqlText = file_get_contents(CUBE_DEV_ROOT_DIR . '/data/dev_config.sql');
        $sqls = explode($separator, $sqlText);
        foreach ($sqls as $item)
        {
            if (!$item)
            {
                continue;
            }
            $sql = $separator . $item;
            $ret = $this->connection->query($sql);
        }

        // add system config db to `sys_sever_setting`
        $db = MEngine_EngineDB::fromDBInfo($dbInfo);

        $info = array();
        $info['host'] = $dbInfo['h'];
        $info['port'] = $dbInfo['P'];
        $info['group_key'] = $sysDBKey;
        $info['cluster_index'] = 0;
        $info['user'] = $dbInfo['u'];
        $info['passwd'] = $dbInfo['p'];
        $info['db_name'] = $sysDBName;
        $ret = $db->insert('sys_db_info', $info, array('password', 'group_key'));

        // create table
        $sqlContent = file_get_contents(CUBE_DEV_ROOT_DIR . '/data/admin.sql');
        $sqlContent = str_replace('{admin_user_table}', $adminUserTable, $sqlContent);

        $creator = new MEngine_MysqlTableCreator($db);
        $creator->createTable($sysDBKey, $sqlContent);

        // set data to dataconfig so that Min DBMan will work.
        MEngine_SysConfig::updateSysConfig($this->buildSysConfig());

        MAdmin_UserRaw::create($this->input['user_account'], $userPwd, array(), 1);

        $this->getConfigInfo();
        $this->popDialog('succ', 'Deployment has been done. Copy the configuration to the destination file.');
    }

    private function checkDeploy()
    {
        $ret = MAdmin_Init::checkInit();

        if ($ret)
        {
            $this->popDialog('succ', 'Deployment has been done. Click <a href="/">HERE</a> to login.');
        }
    }

    private function getDeployData()
    {
        $db = MEngine_EngineDB::fromDBInfo($this->dbInfo);
        $deployData = MEngine_MysqlDeploy::getDeployData($db);
        return $deployData;
    }

    private function buildSysConfig()
    {
        return MEngine_SysConfig::buildSysConfig($this->dbInfo, $this->input['user_table']);
    }

    private function getConfigInfo()
    {
        $data = array();
        $data['sys_config_str'] = MCore_Tool_Conf::formatConfigData($this->buildSysConfig());
        $data['deploy_data_str'] = MCore_Tool_Conf::formatConfigData($this->getDeployData());
        $this->setData($data);
    }

    private function clearSetting()
    {
        $sysDBName = $this->dbInfo['db'];
        $this->connection->selectDB($sysDBName);
        $list = array('sys_sever_setting', 'sys_table_setting', 'sys_kind_setting');
        foreach ($list as $table)
        {
            $sql = 'drop table if exists ' . $table;
            $this->connection->query($sql);
        }

        $userDBName = $this->input['user_db'];
        $adminUserTable = $this->input['user_table'];
        $this->connection->selectDB($userDBName);
        $sql = 'drop table if exists ' . $adminUserTable;
        $this->connection->query($sql);

        $this->popDialog('succ', 'Done');
    }

    /**
     * check the connection by show databases
     */
    private function checkConnection()
    {
        try
        {
            $dbInfo = $this->dbInfo;
            $dbInfo['db'] = '';
            $this->connection = MCore_Min_DBConection::get($dbInfo);
            $dbList = $this->connection->query('show databases');
        }
        catch (MCore_Min_DBException $ex)
        {
            $errorKeys = array('db_host', 'db_port', 'db_user', 'db_pwd');
            $this->setData('error_keys', $errorKeys);
            $this->setError('Can not connect to this database server.');
            return false;
        }

        $sysDBName = $this->dbInfo['db'];
        if ($dbList->where(array('Database' => $sysDBName))->count() == 0)
        {
            $this->setData('error_keys', array('db_name'));
            $this->setError('This database is not existent: ' . $sysDBName);
            return false;
        }

        $userDBName = $this->input['user_db'];
        if ($dbList->where(array('Database' => $userDBName))->count() == 0)
        {
            $this->setData('error_keys', array('user_db'));
            $this->setError('This database is not existent: ' . $userDBName);
            return false;
        }
        return true;
    }

    private function formatDBInfo($data)
    {
        $keys = array('h' => 'db_host', 'u' => 'db_user', 'P' => 'db_port', 'p' => 'db_pwd', 'db' => 'db_name');

        $dbInfo = array();
        foreach ($keys as $k => $key)
        {
            if (!isset($data[$key]))
            {
                throw new Exception('Illegal data format: ' . $key);
            }
            $dbInfo[$k] = $data[$key];
        }
        return $dbInfo;
    }
}
