<?php
/**
 * 从管道输入，输出到Zipkin
 */

date_default_timezone_set("Asia/Shanghai");

require_once __DIR__."/../lighttracer.phar";

if ($argc > 1) {
    $api_url = $argv[1];
} else {
    $api_url = 'http://127.0.0.1:9411/api/v1/spans';
}

if ($argc > 2) {
    \LightTracer\Util\Debug::setDebug($argv[2]);
}

$writer = new \LightTracer\Writer\ZipkinWriter($api_url);

while ($line = fgets(STDIN)) {
    $log = json_decode($line, true);
    $writer->write($log);
}
