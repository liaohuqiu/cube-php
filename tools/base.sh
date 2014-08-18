#!/bin/bash

function make_dir()
{
    ensure_dir $1
}

function ensure_dir()
{
    if [ ! -d $1 ]; then
        exe_cmd "mkdir -p $1"
    fi
}
function link_dir()
{
    if [ -e $2 ];then
        exe_cmd "rm $2"
        exe_cmd "ln -sf $1 $2"
    fi
}

function exe_cmd()
{
    echo $1
    eval $1
}

function add_user
{
    exe_cmd "/usr/sbin/groupadd $1"
    exe_cmd "/usr/sbin/useradd -s /sbin/nologin -g $1 $1"
}

# key, cmd
function crontab_add()
{
    local key=$1
    local cmd=$2
    ( crontab -l 2>/dev/null | grep -Fv $key ; printf -- "$cmd\n" ) | crontab
}

function replace()
{
    local file=$1
    local from=$2
    from=`echo $from | sed -e 's/[]\/$*.^|[]/\\&/g'`
    local to=$3
    # http://stackoverflow.com/questions/13055889/sed-with-literal-string-not-input-file
    to=$(echo $to | sed -e 's/[]\/$*.^|[]/\\&/g')
    cmd="sed -i 's/{{$"$from"}}/"$to"/g' $file"
    exe_cmd "$cmd"
}

# mode file tag_str content
function change_line() 
{
    local mode=$1
    local file=$2
    local tag_str=$3
    local content=$4
    local file_bak=$file".bak"
    local file_temp=$file".temp"
    cp -f $file $file_bak
    if [ $mode == "append" ]; then
        grep -q "$tag_str" $file || echo "$tag_str" >> $file
    else
        cat $file |awk -v mode="$mode" -v tag_str="$tag_str" -v content="$content" '
        {
            if ( index($0, tag_str) > 0) {
                if ( mode == "after"){
                    printf( "%s\n%s\n", $0, content);

                } else if (mode == "before")
                {
                    printf( "%s\n%s\n", content, $0);

                } else if(mode == "replace") 
                {
                    print content;
                }
            } else if ( index ($0, content) > 0) 
            {
                # target conten in line
                # do nothing
            } else
            {
                print $0;
            }
        }' > $file_temp
        mv $file_temp $file
    fi
}
