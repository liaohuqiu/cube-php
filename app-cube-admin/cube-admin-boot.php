<?php
$current_dir = dirname(__FILE__);
$root_dir = dirname($current_dir);

include $root_dir . '/boot-app-cube-admin.php';

// add include dir
Cube::addIncludePath($current_dir . '/include');

define('ADMIN_ROOT_DIR', $current_dir);
