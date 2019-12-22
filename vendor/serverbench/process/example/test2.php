<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use ServerBench\Process\Pool;

$pool = new Pool();

for ($i = 0; $i < 10; ++$i) {
    $pid = $pool->spawn('/usr/bin/php', ['./hello.php']);

    if ($pid > 0) {
        continue;
    }

    echo "error!\n";
}

$pool->waitAll(true);
