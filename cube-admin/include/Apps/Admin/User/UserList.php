<?php
class MApps_Admin_User_UserList extends MApps_AdminPageBase
{
    protected function main()
    {
    }

    protected function outputBody()
    {
        $table_kind_name = MEngine_SysConfig::getSysConfig('admin_user_table');
        $table = array(
            'kind' => $table_kind_name,
            'default_order' => 'order by sid asc',
        );

        $edit = array(
            'edit_url' => '/admin/user/user-edit',
            'can_create' => 1,
            'can_delete' => true,
            'can_edit' => 1,
        );

        $header = array(
            'hide_fields' => array(
                'user_group',
                'status',
                'data',
                'token',
                'salt',
                'pwd',
            ),
            'names' => array(
                'is_sysadmin' => 'system admin',
                'auth_keys' => 'authorization',
                'ctime' => 'create at',
                'mtime' => 'last modified',
            ),
        );

        $conf = array();
        $conf['edit_info'] = $edit;
        $conf['table'] = $table;
        $conf['header'] = $header;
        $c = new MAdmin_Views_ListViewController($conf, MCore_Dao_DB::create());
        $c->render();
    }
}
