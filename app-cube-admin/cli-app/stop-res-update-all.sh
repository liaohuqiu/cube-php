ps -ef |grep app-cube-admin/cli-app/run.py | grep -v grep | awk  '{print "kill " $2}' |sh
