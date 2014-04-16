<?php
$data = array();
$data['engine_db_server'] =  array(
    'h' => '127.0.0.1',
    'u' => 'root',
    'p' => 'root',
    'P' => '3306',
    'server_group_key' => 'group-for-config',
);
$data['engine_db_name'] = 'cube_engine';
$data['admin_table_db_name'] = 'cube_admin';

$data['admin_user_table'] = 's_cube_admin_user';    // the table name to store admin user information

// please do remember to delete this user or change password

$data['admin_init_user_name'] = 'srain';            // initialized user name
$data['admin_init_user_pwd'] = '111111';            // initialized user password

return $data;
