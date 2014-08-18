#!/bin/bash
. ./base.sh

if [ $# != 1 ]; then
    echo 'Useage: sh ' $0 ' appname'
    exit;
fi
app_name=$1
app_dir_name='app-'$1
root_dir=`readlink -f ..`
app_root_dir=$root_dir'/'$app_dir_name
exe_cmd "cd $root_dir"
ensure_dir $app_root_dir
exe_cmd 'cp -rf '$root_dir'/app-cube-demo/* '$root_dir'/'$app_dir_name'/'
exe_cmd "cp $root_dir/boot-app-cube-demo.php $root_dir/boot-app-$app_name.php"

static_dir=$root_dir/htdocs_res/$app_name'-static/'
ensure_dir $static_dir
exe_cmd "cp -rf $root_dir/htdocs_res/cube-demo-static/* $static_dir"

res_dir=$root_dir/htdocs_res/res/$app_name/
ensure_dir $res_dir
exe_cmd "cp -rf $root_dir/htdocs_res/res/cube-demo/* $res_dir"

f_app_boot_php=$app_root_dir/app-boot.php
replace $f_app_boot_php 'cube-demo.php' $app_name'.php'
replace $app_root_dir/include/Apps/Index.php 'cube-demo' $app_name
replace $app_root_dir/include/Apps/AppBase/BasePageApp.php 'cube-demo' $app_name
replace $app_root_dir/include/Apps/AppBase/BasePageApp.php 'Cube Demo' $app_name

exe_cmd "cd $root_dir/app-cube-admin/cli-app/js/"
exe_cmd "sh do-update-res-info.sh"
