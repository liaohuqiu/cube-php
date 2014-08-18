#!/bin/bash
. ./basesh

if [ $# != 1 ]; then
    echo 'Useage: sh ' $0 ' appname'
    exit;
fi
app_name=$1
root_dir=`readlink -f ..`
app_dir=$root_dir'/app-'.$appname
exe_cmd "cd $root_dir"
ensure_dir $app_dir
