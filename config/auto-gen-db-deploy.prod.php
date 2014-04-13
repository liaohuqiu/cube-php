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
        'p' => '',
      ),
    ),
  ),
  'tables' => 
  array (
    's_share_admin_user' => 
    array (
      'id_field' => 'uid',
      'table_num' => '1',
      'table_prefix' => 's_share_admin_user',
      'app' => NULL,
      'db_name' => 'share_admin',
      'tables' => 
      array (
        0 => 
        array (
          'no' => '0',
          'sid' => '1',
        ),
      ),
    ),
    's_share_event_join' => 
    array (
      'id_field' => 'uid',
      'table_num' => '1',
      'table_prefix' => 's_share_event_join',
      'app' => NULL,
      'db_name' => 'share_dev',
      'tables' => 
      array (
        0 => 
        array (
          'no' => '0',
          'sid' => '1',
        ),
      ),
    ),
    's_share_event' => 
    array (
      'id_field' => 'id',
      'table_num' => '1',
      'table_prefix' => 's_share_event',
      'app' => NULL,
      'db_name' => 'share_dev',
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
      'db_name' => 'share_dev',
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
