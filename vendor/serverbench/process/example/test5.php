<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use ServerBench\Process\Loop;
use ServerBench\Process\CliArguments;

$args = new CliArguments([
    'c:' => 'config:'
]);

if (isset($args['config'])) {
    printf("config file -> %s\n", $args['config']);
}

$loop = ServerBench\Process\loop();

$loop->start();

while ($loop()) {
    sleep(3);
    echo "loop\n";
}

echo "stop\n";

$loop->stop();
