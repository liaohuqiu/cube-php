<?php
include dirname(dirname(__FILE__)) . '/app-boot.php';
$api_class_list = array(
    'MApis_Admin_ServerGroup_InfoV1' => 1,
    'MApis_Admin_ServerGroup_InfoV8' => 1,
    'MApis_Admin_ServerGroup_InfoV10' => 1,
);

$path_map = array(
    '/' => '/index',
    '/api' => '/api/api-list',
);

$pre_path_map = array(
    '/img' => '/common/image',   // no extension postfix
);
MCore_Web_Router::addPathMapList($path_map);
MCore_Web_Router::addPrePathMap($pre_path_map);
MCore_Web_Router::addApiClassList($api_class_list);

MCore_Web_Router::addRule('admin/database/server-list', array('server_id', 'from_when'));
MCore_Web_Router::addRule('item-detail', array('item_id', 'type'));

$origin_url = $_SERVER['REQUEST_URI'];
$origin_url = parse_url($origin_url, PHP_URL_PATH);

$request_info = MCore_Web_Router::fetchRequestInfoFromUrl($origin_url);
$dispatcher = new MCore_Web_RequestDispatcher();
$dispatcher->dispatch($request_info);
