<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

if (!isset($_GET['auth']) || $_GET['auth'] != 'iamauth') {
    http_response_code(403);
    header('HTTP/1.0 403 Forbidden');
    exit();
}

require __DIR__ . '/../../../vendor/autoload.php';

use LightService\Jsonrpc\Server\Server;
use LightService\Client\Service as ClientService;

// error_log("uri: {$_SERVER['REQUEST_URI']}");

class UserModule
{
    use \LightService\Util\Explorable;

    public function login($user, $passwd)
    {
        if ($user != 'foo') {
            return 'illigal user';
        }

        if ($passwd != 'bar') {
            return 'wrong passwd, ' . $passwd;
        }

        return array('hello world!', 'pass');
    }

    public function loginWithException()
    {
        throw new \Exception('login with exception!');
    }
}

class HashitModule
{
    public function hash($data)
    {
        // if ($data == 'data2') {
            // header('HTTP/1.0 403 Forbidden');
            // exit();
        // }

        return array($data, md5($data));
    }
}

class FooModule
{
    public function bar()
    {
        return 'bar';
    }
}

class EchoModule
{
    public function hi($sth)
    {
        return $sth;
    }
}

class DataCenterModule
{
    public function getCityList($id)
    {
        return [
            '上海',
            '北京',
            '广州'
        ];
    }

    public function getUserList($id)
    {
        return [
            '小红',
            '小黄',
            '小绿'
        ];
    }

    public function getNoneList($id)
    {
        return null;
    }

    public function getIpList($id)
    {
        return [
            '192.168.1.1',
            '192.168.1.2',
            '192.168.1.3'
        ];
    }
}

$server = new Server([
    'use_msgpack' => false,
    'loader' => function($service, $method) {
        // var_dump('haode');
        // var_dump('------------');
        // var_dump($service);
        // var_dump($method);
        // var_dump('------------');
        if ('login.qlogin' == $method) {
            $method = 'login';
        }

        if (!isset($service) && function_exists($method)) {
            return $method;
        }

        // if ('local' === $module) {
            // return Service::ret(ClientService::get('dev')->call('any.any'));
        // }

        $class = $service . 'Module';

        if (class_exists($class)) {
            return [new $class, $method];
        }
    }
]);

ls_on('server.beforeHandleRequest', function ($event, $ctx, $server) {
    error_log("_______________________________________________________");
    error_log("");
    $ctx->start = microtime(true);
});

ls_on('server.afterHandleRequest', function ($event, $ctx, $server) {
    $ms = (microtime(true) - $ctx->start) * 1000;
    error_log("request use ${ms} ms");
    error_log("");
    error_log("_______________________________________________________");
});

ls_on('server.beforeDispatch', function ($event, $ctx, $call, $server) {
    error_log("{$call->method}");

    if (!$ctx->offsetExists('request_id')) {
        $ctx->request_id = 0;
    }

    $ctx[++$ctx->request_id] = microtime(true);
});

ls_on('server.afterDispatch', function ($event, $ctx, $call, $err, $result, $server) {
    $delta = (microtime(true) - $ctx[$ctx->request_id]) * 1000;

    if ($err) {
        error_log("#{$ctx->request_id}\t{$call->method}\t{$delta}ms\tfailed\t{$err->code}\t{$err->message}");
    } else {
        error_log("#{$ctx->request_id}\t{$call->method}\t{$delta}ms");
    }
});

$server->registerMethod('Echo.hi', function ($ret) {
    return $ret;
});

$msg = file_get_contents('php://input');
error_log("msg: {$msg}");

if (isset($_SERVER['PATH_INFO'])) {
    echo $server->respondLite(substr($_SERVER['PATH_INFO'], 1), $msg);
} else {
    echo $server->respond(('GET' == $_SERVER['REQUEST_METHOD']) ? $_GET : $msg);
}
