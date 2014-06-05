<?php
$data['module_list'] = array(
    'database' => array(
        'root_path' => '/admin',
        'name' => 'System',
        'url'  => '/database/server-list',
        'auth_key' => 'system',
        'units'=> array(
            array(
                'name' => 'DataBase',
                'list' => array(
                    array(
                        'name' => 'Table Config',
                        'url' => '/database/table-config',
                    ),
                    array(
                        'name' => 'DB Servers',
                        'url' => '/database/server-list',
                    ),
                    array(
                        'name' => 'Table List',
                        'url' => '/database/table-list',
                    ),
                    array(
                        'name' => 'Create Table',
                        'url' => '/database/table-new',
                    ),
                    array(
                        'name' => 'Edit Table',
                        'url' => '/database/table-edit',
                    ),
                    array(
                        'name' => 'Query Table',
                        'url' => '/database/table-query',
                    ),
                )
            ),
        )
    ),
    'user' => array(
        'root_path' => '/admin/user',
        'name' => 'User',
        'url'  => 'user-list',
        'role' => 'master',
        'auth_key' => 'user',
        'units'=> array(
            array(
                'name' => 'user',
                'list' => array(
                    array(
                        'name' => 'User List',
                        'url' => 'user-list',
                    ),
                )
            )
        ),
    )
);
return $data;
