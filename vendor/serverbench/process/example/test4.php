<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use ServerBench\Process\Loop;

$loop = ServerBench\Process\loop();

$loop->start();

while ($loop()) {
    sleep(3);
    echo "loop\n";
}

echo "stop\n";

$loop->stop();
