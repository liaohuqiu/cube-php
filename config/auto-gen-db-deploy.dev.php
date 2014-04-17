<?php
$data = array (
  'servers' => 
  array (
    1 => 
    array (
      'master' => 
      array (
        'h' => '127.0.0.1',
        'P' => '3306',
        'u' => 'root',
        'p' => 'root',
      ),
    ),
  ),
  'tables' => 
  array (
    's_cube_admin_user' => 
    array (
      'id_field' => 'uid',
      'table_num' => '1',
      'table_prefix' => 's_cube_admin_user',
      'app' => NULL,
      'db_name' => 'cube_admin',
      'tables' => 
      array (
        0 => 
        array (
          'no' => '0',
          'sid' => '1',
        ),
      ),
    ),
    's_user_info' => 
    array (
      'id_field' => 'uid',
      'table_num' => '1',
      'table_prefix' => 's_user_info',
      'app' => NULL,
      'db_name' => 'cube_dev',
      'tables' => 
      array (
        0 => 
        array (
          'no' => '0',
          'sid' => '1',
        ),
      ),
    ),
  ),
);
return $data;
