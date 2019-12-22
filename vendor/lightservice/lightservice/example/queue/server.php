<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require __DIR__ . '/../../vendor/autoload.php';

use LightService\Server\Service;
use PhpAmqpLib\Connection\AMQPStreamConnection;


class QueueHandlerModule
{
    public function handle($message)
    {
        return $message;
    }
}

$s = Service::create('jsonrpc', function($module, $method, $params, $id) {
    error_log('id: ' . $id);
    error_log(print_r($params, true));
    error_log($method);

    if (!isset($module) && function_exists($method)) {
        return $method;
    }

    if ('forbidden' === $method) {
        return Service::ret('Forbidden 403');
    }

    $class = $module . 'Module';

    if (class_exists($class)) {
        $callable = array(new $class, $method);
        return is_callable($callable) ? $callable : NULL;
    }
});

$connection = new AMQPStreamConnection('10.59.72.51', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->exchange_declare('test1234', 'direct', false, true, false);
list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

$channel->queue_bind($queue_name, 'test1234');

$callback = function($msg) use ($s) {
//    echo ' [x] ', $msg->body, "\n";
    echo $s->respond($msg->body);
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
