<?php
$data['module_list'] = array(
    'database' => array(
        'root_path' => '/admin/database',
        'name' => 'DataBase',
        'url'  => 'server-list',
        'role' => 'master',
        'auth_key' => 'db',
        'units'=> array(
            array(
                'name' => '数据库管理',
                'list' => array(
                    array(
                        'name' => '数据库编号',
                        'url' => 'server-list',
                        'auth_key' => 'master',
                        'role' => 'master',
                    ),
                    array(
                        'name' => '表列表',
                        'url' => 'table-list',
                        'auth_key' => 'master',
                    ),
                    array(
                        'name' => '创建表',
                        'url' => 'table-new',
                        'auth_key' => 'master',
                    ),
                    array(
                        'name' => '修改表',
                        'url' => 'table-edit',
                        'auth_key' => 'master',
                    ),
                    array(
                        'name' => '查询',
                        'url' => 'table-query',
                        'auth_key' => 'master',
                    ),
                )
            ),
        )
    ),
);
return $data;
