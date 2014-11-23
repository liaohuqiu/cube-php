<?php
class MApps_Admin_User_UserList extends MApps_AdminPageBase
{
    protected function main()
    {
    }

    protected function outputBody()
    {
        // sample config
        $table_kind_name = MEngine_SysConfig::getSysConfig('admin_user_table');
        $table = array(
            'kind' => $table_kind_name,
            'default_order' => 'order by sid asc',
            // todo
            'search' => array(
                'fields' => array(
                    'email'
                ),
                'select_field' => array(
                ),
            ),
        );

        $value_name_map = array(
            '1' => 'xxxx',
            '2' => '22'
        );
        $name_value_map = array();

        $quick_select = array(
            'cate' => array(
                "field" => "cate",
                'des' => 'Category',
                'default_value' => 0,
                'value_name_map' => $value_name_map,
                'name_value_map' => $name_value_map,
            ),
        );

        $edit = array(
            'edit_url' => '/admin/user/user-edit',
            'can_create' => 1,
            'can_delete' => true,
            'can_edit' => 1,
        );

        $header = array(
            'no_sort_filds' => array(
            ),
            // will filter by the value of this field when click an item.
            'this_value_fields' => array(
            ),
            // only display these fields
            'only_display_fields' => array(
            ),
            'hide_fields' => array(
                'user_group',
                'status',
                'data',
                'token',
                'salt',
                'pwd',
            ),
            // even the field not in table field will show
            'names' => array(
                'is_sysadmin' => 'system admin',
                'auth_keys' => 'authorization',
                'ctime' => 'create at',
                'mtime' => 'last modified',
            ),
            // default is center
            'align' => array(
                'email' => 'left',
            ),
            'order' => array(
                'uid',
                'email',
                'auth_keys',
                'is_sysadmin',
            ),
        );

        $conf = array();
        $conf['edit_info'] = $edit;
        $conf['table'] = $table;
        $conf['header'] = $header;
        $conf['quick_select'] = $quick_select;
        // $conf['format_data_item'] = $call_back;      // data from db will be filter by this callback
        // $conf['format_display_data_item'] = $call_back;
        $c = new MAdmin_Views_ListViewController($conf, MCore_Dao_DB::create());
        $c->render();
    }
}
