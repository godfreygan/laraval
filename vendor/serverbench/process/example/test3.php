<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use ServerBench\Process\Pool;

$pool = new Pool();

$pool->start(10, ['/bin/sleep', ['10']]);
$pool->waitAll(true);
