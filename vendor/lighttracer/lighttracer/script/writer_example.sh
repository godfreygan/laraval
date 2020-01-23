php examples/basic.php
LOG_PATH=/tmp/lighttracer/trace_log`date '+%Y%m%d'`.log
tail -n 1 $LOG_PATH | jq ""
tail -n 1 $LOG_PATH | php script/cat_writer.php 127.0.0.1 2280 1
tail -n 1 $LOG_PATH | php script/zipkin_writer.php 'http://127.0.0.1:9411/api/v1/spans' 1
