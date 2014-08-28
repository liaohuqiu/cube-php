#!/bin/bash

if [ $# != 1 ]; then
    echo 'Useage: sh ' $0 ' cube-source-path'
    exit;
fi
from=`readlink -f $1`

if [ ! -d $from ] || [ ! -d $from'/cube-core' ]; then
    echo $from 'is not cube source directory'
    exit;
fi
echo 'update from: ' $from;
dir=`pwd`

function exe_cmd() 
{
    echo $1
    eval $1
}

function update_dir() 
{
    exe_cmd "rm -rf $1"
    exe_cmd "cp -rf $from/$1 ./$1"
}

exe_cmd 'cd ..'
update_dir 'cube-core'
update_dir 'app-cube-admin'
update_dir 'app-cube-demo'

update_dir 'htdocs_res/cube-admin-mix'
update_dir 'htdocs_res/cube-demo-static'

update_dir 'htdocs_res/res/admin'
update_dir 'htdocs_res/res/cube'
update_dir 'htdocs_res/res/cube-demo'
