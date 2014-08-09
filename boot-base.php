<?php
date_default_timezone_set('Asia/Chongqing');

$boot_dir = dirname(__FILE__);

// set up for cube
define('APP_NAME', 'cube-demo'); // change to you app name
define('CUBE_ROOT_DIR', $boot_dir . '/cube-core');
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
    define('ENV_TAG', 'pre');
}
else if (gethostname() == 'xxxxxx')
{
    define('ENV_TAG', 'test');
}
else if (gethostname() == 'xxxxxx')
{
    define('ENV_TAG', 'dev');
}
else
{
    // here we set to demo
    define('ENV_TAG', 'demo');
}

// load cube
require CUBE_ROOT_DIR . '/cube-boot.php';

// boot
Cube::boot();
