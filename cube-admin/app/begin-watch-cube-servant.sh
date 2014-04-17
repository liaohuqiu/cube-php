export PATH=/bin:/usr/bin:/sbin:/usr/sbin:/usr/local/bin:/usr/local/sbin
function exe_cmd()
{
    echo $1
    eval $1
}
app_root_dir=`readlink -f ../`
exe_cmd "python $app_root_dir/app/run.py $app_root_dir/app/engine/start-cube-servant.sh"
