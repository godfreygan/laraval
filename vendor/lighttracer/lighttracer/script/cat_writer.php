<?php
/**
 * 从管道输入，输出到CAT
 */

date_default_timezone_set("Asia/Shanghai");

require_once __DIR__."/../lighttracer.phar";

if ($argc > 1) {
    $host = $argv[1];
} else {
    $host = '127.0.0.1';
}

if ($argc > 2) {
    $port = $argv[2];
} else {
    $port = 2280;
}

if ($argc > 3) {
    \LightTracer\Util\Debug::setDebug($argv[3]);
}

$writer = new \LightTracer\Writer\CatWriter($host, $port);

while ($line = fgets(STDIN)) {
    $log = json_decode($line, true);
    $writer->write($log);
}
