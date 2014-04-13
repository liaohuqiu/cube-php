<?php
class MApps_Admin_Database_TableNewSubmit extends MApps_AdminPageBase
{
    protected function main()
    {
        $this->dbName = MCore_Tool_Input::clean('r', 'db_name', 'str');
        $this->serverGroupKey = MCore_Tool_Input::clean('r', 'server_group_key', 'str');
        $this->sql = MCore_Tool_Input::clean('r', 'sql', 'str');
        $ret = MEngine_MysqlTableCreator::create($this->serverGroupKey, $this->dbName, $this->sql);
        $this->go2('table-list');
        exit;
    }
}
