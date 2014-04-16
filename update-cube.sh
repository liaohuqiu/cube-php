#!/bin/bash

if [ $# != 1 ]; then
    echo 'Useage: sh ' $0 ' cube-source-path'
    exit;
fi
from=`readlink -f $1`
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

update_dir 'cube'
update_dir 'cube-admin'
update_dir 'cube-admin'
update_dir 'htdocs_res/cube-admin-mix'
update_dir 'htdocs_res/res/cube'
update_dir 'htdocs_res/res/admin'
