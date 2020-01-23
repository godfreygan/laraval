<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use ServerBench\Process\Pool;

$pool = new Pool();

for ($i = 0; $i < 10; ++$i) {
    $pid = $pool->fork();

    if ($pid > 0) {
        continue;
    } elseif (0 === $pid) {
        echo 'pid: ', posix_getpid(), " start\n";
        sleep(10);
        echo 'pid: ', posix_getpid(), " stop\n";
        exit(0);
    } else {
        echo "error!\n";
    }
}

$pool->waitAll(true);
