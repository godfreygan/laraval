<?php
require __DIR__ . '/../../vendor/autoload.php';

use LightService\Client\Service;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// config
Service::importConf(
    array(
        'dev' => array(
            'type' => 'queue',
            'protocol' => 'jsonrpc',
            'conf' => array(
                'push' => function ($message) {
                        $connection = new AMQPStreamConnection('10.59.72.51', 5672, 'admin', 'admin');
                        $channel = $connection->channel();

                        $channel->exchange_declare('test1234', 'direct', false, true, false);

//                        $data = implode(' ', array_slice($argv, 1));
//                        if(empty($data)) $data = "info: Hello World!";
                        $msg = new AMQPMessage($message);

                        $channel->basic_publish($msg, 'test1234');

                        echo " [x] Sent ", $message, "\n";

                        $channel->close();
                        $connection->close();
                        return true;
                    }
            )
        )
    )
);

// get service
$s = Service::get('dev');

// get specified module
$queue = $s->module('QueueHandler');

echo "\n----------\n";
echo "\nqueue->handler\n";

$message = 'testforcaoyi';
$rep = $queue->handle($message);

if (!$queue->errno()) {
    echo 'rep : ';
    var_dump($rep);
} else {
    echo 'errno  : ', $queue->errno(), "\n";
    echo 'errstr : ', $queue->errstr(), "\n";
}

echo "\n----------\n";
