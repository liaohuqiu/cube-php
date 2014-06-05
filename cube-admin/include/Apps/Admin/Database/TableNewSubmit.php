<?php
class MApps_Admin_Database_TableNewSubmit extends MApps_AdminPageBase
{
    protected function main()
    {
        $this->dbName = MCore_Tool_Input::clean('r', 'db_name', 'str');
        $this->serverGroupKey = MCore_Tool_Input::clean('r', 'server_group_key', 'str');
        $this->sql = MCore_Tool_Input::clean('r', 'sql', 'str');
        $onlySchema = MCore_Tool_Input::clean('r', 'only_schema', 'int');
        $creator = new MEngine_MysqlTableCreator();
        $ret = $creator->createTable($this->serverGroupKey, $this->dbName, $this->sql, '', $onlySchema);
        $this->go2('table-list');
        exit;
    }
}
