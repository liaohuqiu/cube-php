<?php
$current_dir = dirname(__FILE__);
$root_dir = dirname($current_dir);

include $root_dir . '/boot.php';

// add include dir
Cube::addIncludePath($current_dir . '/include');

define('CUBE_ADMIN_ROOT_DIR', $current_dir);

// for smarty
define('SMARTY_CLASS_PATH', $root_dir . '/vendor/smarty/libs/Smarty.class.php');
define('SMARTY_WRITALE_DIR', WRITABLE_DIR . '/smarty');
