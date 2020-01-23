<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

require __DIR__ . '/../vendor/autoload.php';

use LightService\Jsonrpc\Server\Server;

LightTracer\Plugin\LightService::init([
    'endpoint_name'  => 'api.s2.com',
    'trace_log_path' => 'd:/trace_log/s2',
    'trace_log_span' => 'day',
]);

$server = new Server([
    'use_msgpack' => false,
    'loader'      => function ($service, $method) {
        $class = $service . 'Module';
        if (class_exists($class)) {
            return [new $class, $method];
        }
    }
]);

class GoodModule
{
    public function show($id)
    {
        $trace_id = LightTracer\GlobalTracer::traceId();
        LightTracer\GlobalTracer::logEvent('rpc-service-good trace-id: ' . $trace_id);
        LightTracer\GlobalTracer::logEvent('start read db');

        usleep(50000); // 50毫秒
        LightTracer\GlobalTracer::logEvent('end read db');

        return [
            'id'        => $id,
            'good_id'   => 1,
            'good_name' => 'iPhone X'
        ];
    }
}

$msg = file_get_contents('php://input');

if (isset($_SERVER['PATH_INFO'])) {
    echo $server->respondLite(substr($_SERVER['PATH_INFO'], 1), $msg);
} else {
    echo $server->respond(('GET' == $_SERVER['REQUEST_METHOD']) ? $_GET : $msg);
}
