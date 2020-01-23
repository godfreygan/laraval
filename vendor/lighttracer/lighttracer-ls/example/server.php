<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

require __DIR__ . '/../vendor/autoload.php';

use LightService\Jsonrpc\Server\Server;

class UserModule
{
    public function login($username, $password)
    {
        return [
            'hello',
            'pass'
        ];
    }
}

$server = new Server([
    'use_msgpack' => false,
    'loader'      => function ($service, $method) {
        $class = $service . 'Module';
        if (class_exists($class)) {
            return [new $class, $method];
        }
    }
]);

ls_on('server.beforeHandleRequest', function ($event, $ctx, $server) {
    error_log("___________________________beforeHandleRequest____________________________");
    error_log("");

    $ctx->start = microtime(true);
});

ls_on('server.afterHandleRequest', function ($event, $ctx, $server) {
    error_log("_________________________________afterHandleRequest______________________");
    $ms = (microtime(true) - $ctx->start) * 1000;
    error_log("request use ${ms} ms");
    error_log("");
});

ls_on('server.beforeDispatch', function ($event, $ctx, $call, $server) {
    error_log("_________________________________beforeDispatch______________________");

    if (!$ctx->offsetExists('request_id')) {
        $ctx->request_id = 0;
    }

    $ctx[++$ctx->request_id] = microtime(true);
});

ls_on('server.afterDispatch', function ($event, $ctx, $call, $err, $result, $server) {
    error_log("_________________________________afterDispatch______________________");
    $delta = (microtime(true) - $ctx[$ctx->request_id]) * 1000;

    if ($err) {
        error_log("#{$ctx->request_id}\t{$call->method}\t{$delta}ms\tfailed\t{$err->code}\t{$err->message}");
    } else {
        error_log("#{$ctx->request_id}\t{$call->method}\t{$delta}ms");
    }
});

$msg = file_get_contents('php://input');
error_log("msg: {$msg}");

if (isset($_SERVER['PATH_INFO'])) {
    echo $server->respondLite(substr($_SERVER['PATH_INFO'], 1), $msg);
} else {
    echo $server->respond(('GET' == $_SERVER['REQUEST_METHOD']) ? $_GET : $msg);
}
