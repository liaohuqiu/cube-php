watch:	/home/huqiu/git/cube-php/htdocs_res/res
watch:	/home/huqiu/git/cube-php/htdocs_res/res/admin
watch:	/home/huqiu/git/cube-php/htdocs_res/res/admin/css
watch:	/home/huqiu/git/cube-php/htdocs_res/res/admin/js
watch:	/home/huqiu/git/cube-php/htdocs_res/res/cube
watch:	/home/huqiu/git/cube-php/htdocs_res/res/cube/css
watch:	/home/huqiu/git/cube-php/htdocs_res/res/cube/i
watch:	/home/huqiu/git/cube-php/htdocs_res/res/cube/i/base
watch:	/home/huqiu/git/cube-php/htdocs_res/res/cube/js
watch:	/home/huqiu/git/cube-php/htdocs_res/res/cube/js/base
watch:	/home/huqiu/git/cube-php/htdocs_res/res/cube/js/core
watch:	/home/huqiu/git/cube-php/htdocs_res/res/cube/js/core/ajax
watch:	/home/huqiu/git/cube-php/htdocs_res/res/cube/js/core/cookie
watch:	/home/huqiu/git/cube-php/htdocs_res/res/cube/js/core/dialog
watch:	/home/huqiu/git/cube-php/htdocs_res/res/cube/js/core/popup
watch:	/home/huqiu/git/cube-php/htdocs_res/res/cube/js/core/switch
watch:	/home/huqiu/git/cube-php/htdocs_res/res/cube/js/core/tool
node update-res-info.js /home/huqiu/git/cube-php/htdocs_res/res /home/huqiu/git/cube-php/cube-admin/app/js/auto-gen-res-list-info.json
php dispatch-res-info.php -f /home/huqiu/git/cube-php/cube-admin/app/js/auto-gen-res-list-info.json -t /home/huqiu/git/cube-php/htdocs_res/res
update res info
[2014-04-16 15:55:39] writeDataConfig: auto-gen-res-info
[2014-04-16 15:55:39] save version.js:	/home/huqiu/git/cube-php/htdocs_res/res/version.js
mv /home/huqiu/git/cube-php/htdocs_res/res/version.js.tmp /home/huqiu/git/cube-php/htdocs_res/res/version.js
