<?php

use Saber\MQ\Kafka\Producer;

include_once '../../../vendor/autoload.php';

try {
    //配置参数 详见：https://github.com/edenhill/librdkafka/blob/v0.9.2/CONFIGURATION.md
    $mq_config = require __DIR__ . '/config.php';

    $producer = new Producer($mq_config['connection1'], ...array_values($mq_config['producers']['p1']));
    $producer->push( 'message no:' . uniqid());

    $producer->close();
} catch (Exception $e) {
    echo $e->getMessage();
}
