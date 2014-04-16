<?php
date_default_timezone_set('Asia/Chongqing');

$boot_dir = dirname(__FILE__);

// set up for cube
define('APP_NAME', 'cube-test-app'); // change to you app name
define('CUBE_ROOT_DIR', $boot_dir . '/cube');
define('CONFIG_DATA_DIR', $boot_dir . '/config');
define('WRITABLE_DIR', $boot_dir . '/writable');

// config for different enviroment
// You should implement this according your situation
if (gethostname() == 'xxxxxx')
{
    define('ENV_TAG', 'prod');
}
else if (gethostname() == 'xxxxxx')
{
    define('ENV_TAG', 'test');
}
else
{
    define('ENV_TAG', 'dev');
}

// load cube
require CUBE_ROOT_DIR . '/cube-core.php';

// set include dir
Cube::addIncludePath($boot_dir . '/include');

// boot
Cube::boot();
