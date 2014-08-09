<?php
class MApps_Admin_Database_DbList extends MApps_AdminPageBase
{
    protected function main()
    {
    }

    protected function outputBody()
    {
        $table = array(
            'kind' => 'sys_db_info',
            'default_order' => 'order by sid asc',
        );

        $edit = array(
            'edit_url' => '/admin/common/engine-table-item-edit',
            'can_create' => 1,
            // 'can_delete' => true,
            'can_edit' => 1,
        );

        $conf = array();
        $conf['edit_info'] = $edit;
        $conf['table'] = $table;
        $c = new MAdmin_Views_ListViewController($conf, MEngine_EngineDB::fromConfig());
        $c->render();
    }
}
