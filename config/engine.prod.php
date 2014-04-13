<?php
$data = array();
$data['engine_db_server'] =  array(
    'h' => '127.0.0.1',
    'u' => 'root',
    'p' => 'root',
    'P' => '3306',
    'server_group_key' => 'a-name-to-group-some-databases',
);
$data['engine_db_name'] = 'db_name_to_store_config_info';
$data['admin_table_db_name'] = 'db_name_to_store_amdin_info';

$data['admin_user_table'] = 's_at_admin_user';      // the table name to store admin user information
$data['admin_init_user_name'] = 'srain';            // initialized user name
$data['admin_init_user_pwd'] = 'if-not-here-where'; // initialized user password

return $data;
