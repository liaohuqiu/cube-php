<?php
class MApps_Admin_Database_TableNew extends MApps_AdminPageBase
{
    protected function main()
    {
        $keys = MEngine_EngineDB::fromConfig()->select('sys_db_info', array('group_key'))->getFields('group_key');
        $keys = array_combine($keys, $keys);

        $data = array();
        $data['serverGroupOptions'] = MCore_Str_Html::options($keys);
        $this->getView()->setPageData($data);
    }

    protected function outputBody()
    {
        $this->getView()->display('admin/database/table_new.html');
    }
}
