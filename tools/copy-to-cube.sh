#!/bin/bash

if [ $# != 3 ]; then
    echo 'Useage: sh ' $0 ' source cube root, destination scube root, file relative path'
    exit;
fi

from=`readlink -f $1`
to=`readlink -f $2`
file=$3

if [ ! -d $from ] || [ ! -d $from'/cube-core' ]; then
    echo $from 'is not cube source directory'
    exit;
fi

if [ ! -d $to ] || [ ! -d $to'/cube-core' ]; then
    echo $to 'is not cube source directory'
    exit;
fi

echo 'update: ' $file;

function exe_cmd() 
{
    echo $1
    eval $1
}
cmd="cp $from/$file $to/$file"
exe_cmd "$cmd"

