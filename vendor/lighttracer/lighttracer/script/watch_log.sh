#!/bin/bash
# sudo yum install inotify-tools

HOME_DIR=`cd $(dirname $0);pwd`
inotifywait -mrq --format '%w%f' -e modify,create $1 | php $HOME_DIR/watch_output.php