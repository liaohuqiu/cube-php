<?php
include dirname(dirname(__FILE__)) . '/boot.php';
$data = ['name' => 'srain'];
$key = 'test';
MCore_Proxy_Cache::setObj($key, $data, -1);
$ret = MCore_Proxy_Cache::getObj($key);
var_dump($ret);
