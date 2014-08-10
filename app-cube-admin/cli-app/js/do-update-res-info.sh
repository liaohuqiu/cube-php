#!/bin/bash
src=`readlink -f ../../../htdocs_res/res/`
dest=`pwd`"/auto-gen-res-list-info.json"
path_for_version_js=$src
node update-res-info.js "$src" "$dest"
php dispatch-res-info.php -f "$dest" -t "$path_for_version_js"
