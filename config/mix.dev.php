<?php
$data = array();
$data['static_res_host'] = 's.cube-php.com';      // change to your resource host
$data['cube-servant-port'] = 9697;
$data['cube-servant-list'] = array('http://127.0.0.1:9697');
$data['mcache-servers'] = array(
    array('127.0.0.1', 11211),
);
return $data;
