<?php
include dirname(dirname(__FILE__)) . '/app-boot.php';

$api_class_list = array(
    'MApis_Admin_ServerGroup_InfoV1' => 1,
    'MApis_Admin_ServerGroup_InfoV8' => 1,
    'MApis_Admin_ServerGroup_InfoV10' => 1,
);

$path_map = array(
    '/' => 'admin-index',
    '/admin' => 'admin-index',
    '/init' => 'init/init-index',
);
MCore_Web_Router::addPathMapList($path_map);
MCore_Web_Router::addApiClassList($api_class_list);
MCore_Web_Router::addRule(array('admin/database/server-list', array('server_id', 'from_when')));

$origin_url = $_SERVER['REQUEST_URI'];
$origin_url = parse_url($origin_url, PHP_URL_PATH);

$request_info = MCore_Web_Router::fetchRequestInfoFromUrl($origin_url);
$dispatcher = new MCore_Web_RequestDispatcher();
$dispatcher->dispatch($request_info);
