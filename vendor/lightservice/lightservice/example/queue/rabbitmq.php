<?php
require __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('10.59.72.51', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->exchange_declare('new_test_1', 'fanout', false, false, true);

for ($i = 0; $i < 500; $i++) {
    $data .= 'a';
}

$msg = new AMQPMessage($data);

$start = time();
$i = 0;
while ($i < 50000) {
    $i ++ ;
    $channel->basic_publish($msg, 'new_test_1');
    echo " [". $i ."] Sent ", $data, "\n";
}
$end = time();

$channel->close();
$connection->close();

echo "\n-----" . ($end - $start) . "-----\n";
