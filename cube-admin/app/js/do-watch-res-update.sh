#!/bin/bash
src=`readlink -f ../../../htdocs_res/res/`
node watch-res-update.js "$src"
