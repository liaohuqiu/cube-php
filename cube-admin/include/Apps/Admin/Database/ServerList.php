<?php
class MApps_Admin_Database_ServerList extends MApps_AdminPageBase
{
    protected function main()
    {
    }
    protected function outputBody()
    {
        $table = array(
            'kind' => 'server_setting',
            'default_order' => 'order by sid asc',
        );

        $edit = array(
            'edit_url' => '/admin/list-item/edit.php',
            'can_create' => 1,
            // 'can_delete' => true,
            'can_edit' => 1,
        );

        $conf = array();
        $conf['edit_info'] = $edit;
        $conf['table'] = $table;
        $c = new MAdmin_Views_ListViewController($conf, MEngine_EngineDB::getEngineDataOne());
        $c->render();
    }
}
