export PATH=/bin:/usr/bin:/sbin:/usr/sbin:/usr/local/bin:/usr/local/sbin
function exe_cmd()
{
    echo $1
    eval $1
}
current_dir=`pwd`
app_root_dir=`readlink -f ../../`
exe_cmd "python $current_dir/run.py $current_dir/js/do-watch-res-update.sh $app_root_dir/htdocs_res/res/"
