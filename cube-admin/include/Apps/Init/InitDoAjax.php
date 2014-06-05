<?php
class MApps_Init_InitDoAjax extends MApps_AdminAjaxBase
{
    private $dbInfo;
    private $dbList;
    private $connection;
    private $input;

    protected function checkAuth()
    {
    }

    protected function main()
    {
        $data = $this->request->getData('data', 'r', 'json');
        $cmd = $this->request->getData('cmd');

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
            if ($this->checkDB())
            {
                $this->getConfigInfo();
            }
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
        $adminTableDBName = $this->input['user_db'];
        $adminUserTable = $this->input['user_table'];
        $sysDBKey = $this->input['db_key'];

        // check input
        $existent = $this->checkHasDB($sysDBName);
        if ($existent)
        {
            $this->setData('error_keys', array('db_name'));
            $this->setError('This database has been existent: ' . $sysDBName);
            return;
        }
        $existent = $this->checkHasDB($userDBName);
        if ($existent)
        {
            $this->setData('error_keys', array('user_db'));
            $this->setError('This database has been existent: ' . $userDBName);
            return;
        }
        if (!$userPwd)
        {
            $this->setData('error_keys', array('user_pwd'));
            $this->setError('System admin user password is emtpy');
            return;
        }

        // create databases
        MEngine_EngineDB::createDB($this->connection, $sysDBName);
        MEngine_EngineDB::createDB($this->connection, $userDBName);

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

        // add system config db to `server_setting`
        $db = MEngine_EngineDB::fromDBInfo($dbInfo);

        $info = array();
        $info['host'] = $dbInfo['h'];
        $info['port'] = $dbInfo['P'];
        $info['group_key'] = $sysDBKey;
        $info['user'] = $dbInfo['u'];
        $info['passwd'] = $dbInfo['p'];
        $ret = $db->insert('server_setting', $info, array());

        // create table
        $sqlContent = file_get_contents(CUBE_DEV_ROOT_DIR . '/data/admin.sql');
        $sqlContent = str_replace('{admin_user_table}', $adminUserTable, $sqlContent);

        $creator = new MEngine_MysqlTableCreator($db);
        $creator->createTable($sysDBKey, $adminTableDBName, $sqlContent);

        // set data to dataconfig so that Min DBMan will work.
        MEngine_SysConfig::updateSysConfig($this->getSysConfig());
        MCore_Min_TableConfig::setDeployData($this->getDeployData());

        MAdmin_UserRaw::create($this->input['user_account'], $userPwd, array(), 1);
        $this->getConfigInfo();
    }

    private function getDeployData()
    {
        $db = MEngine_EngineDB::fromDBInfo($this->dbInfo);
        $deployData = MEngine_MysqlDeploy::getDeployData($db);
        return $deployData;
    }

    private function getSysConfig()
    {
        $sysConfig = array();
        $sysConfig['sys_config_db'] = $this->dbInfo;
        $sysConfig['admin_user_table'] = $this->input['user_table'];
        return $sysConfig;
    }

    private function getConfigInfo()
    {
        $data = array();
        $data['sys_config_str'] = MCore_Tool_Conf::formatConfigData($this->getSysConfig());
        $data['deploy_data_str'] = MCore_Tool_Conf::formatConfigData($this->getDeployData());
        $this->setData($data);
    }

    private function clearSetting()
    {
        $sysDBName = $this->dbInfo['db'];
        $existent = $this->checkHasDB($sysDBName);
        if ($existent)
        {
            $sql = 'drop database ' . $sysDBName;
            $this->connection->query($sql);
        }

        $userDBName = $this->input['user_db'];
        $existent = $this->checkHasDB($userDBName);
        if ($existent)
        {
            $sql = 'drop database ' . $userDBName;
            $this->connection->query($sql);
        }

        $this->popDialog('succ', 'Done');
    }

    private function checkDB()
    {
        $sysDBName = $this->dbInfo['db'];
        $existent = $this->checkHasDB($sysDBName);
        if (!$existent)
        {
            $this->setData('error_keys', array('db_name'));
            $this->setError('This database is not existent: ' . $sysDBName);
            return false;
        }

        $userDBName = $this->input['user_db'];
        $existent = $this->checkHasDB($userDBName);
        if (!$existent)
        {
            $this->setData('error_keys', array('user_db'));
            $this->setError('This database is not existent: ' . $userDBName);
            return false;
        }
        return true;
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
            $this->dbList = $this->connection->query('show databases');
        }
        catch (MCore_Min_DBException $ex)
        {
            $errorKeys = array('db_host', 'db_port', 'db_user', 'db_pwd');
            $this->setData('error_keys', $errorKeys);
            $this->setError('Can not connect to this database server.');
            return false;
        }
        return true;
    }

    /**
     * check is the database by given name is in the database server
     */
    private function checkHasDB($dbName)
    {
        if ($this->dbList->where(array('Database' => $dbName))->count() == 0)
        {
            return false;
        }
        else
        {
            return true;
        }
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
