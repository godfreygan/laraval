<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

require __DIR__ . '/../vendor/autoload.php';

use LightService\Jsonrpc\Server\Server;
use LightService\Client\Service as Client;

LightTracer\Plugin\LightService::init([
    'endpoint_name'  => 'api.s1.com',
    'trace_log_path' => 'd:/trace_log/s1',
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

class UserModule
{
    public function server()
    {
        return new Client([
            'dev' => [
                'type'                => 'jsonrpc',
                'url'                 => 'http://localhost:9002',
                // 'idgen' => $generateId,
                // 'enable_method_path' => true,
                'enable_method_query' => true,
                'options'             => [
                    'query'        => ['auth' => 'iamauth'],
                    'header'       => [
                        'header-you-like: value-you-mind'
                    ],
                    'exec_timeout' => 1000,
                ]
            ]
        ]);
    }

    public function login($username, $password)
    {
        $good     = [];
        $trace_id = LightTracer\GlobalTracer::traceId();
        LightTracer\GlobalTracer::logEvent('rpc-service-user trace-id: ' . $trace_id);

        LightTracer\GlobalTracer::logEvent('start request goods info ');
        $good = $this->server()->client('dev')->stub('good')->show(1)->wait();
//        throw new Exception("lalala", 1000);

        LightTracer\GlobalTracer::logEvent('end request goods info ');
        return [
            'hello #' . $username,
            'pass #' . $password,
            'good' => $good
        ];
    }
}

$msg = file_get_contents('php://input');

if (isset($_SERVER['PATH_INFO'])) {
    echo $server->respondLite(substr($_SERVER['PATH_INFO'], 1), $msg);
} else {
    echo $server->respond(('GET' == $_SERVER['REQUEST_METHOD']) ? $_GET : $msg);
}
