<?php

require '../vendor/autoload.php';

use LightService\Client\Service;
use LightService\Util\IdGen;
use LightService\ErrorResult;
use LightService\Results;

ls_on('client.beforeCall', function ($event, $ctx, $call) {
    print_r($call);
    $ctx->starttime = microtime(true);
    echo $call;
});

ls_on('client.afterCall', function ($event, $ctx, $call, $err, $result) {
    $endtime = microtime(true);
    $delta   = ($endtime - $ctx->starttime) * 1000;

    echo "time used: $delta ms\n";
});

function example($name, $case)
{
    echo "\n-------------------------------------------------------------------------\n";

    try {
        $rep = call_user_func($case);
        echo "rep: \n";

        if ($rep instanceof Results) {
            foreach ($rep as $i => $v) {
                echo "#$i ", print_r($v, true), "\n";
            }
        } else {
            print_r($rep);
        }
    } catch (Exception $err) {
        $err_result = ErrorResult::fromException($err);
        echo "err: {$err_result}\n";
    } finally {
        echo "\n-------------------------------------------------------------------------\n";
    }
}

// custom id generator
$generateId = new IdGen(function () {
    return mt_rand(1, 9999);
});

$service = new Service([
    'dev' => [
        'type'                => 'jsonrpc',
        'url'                 => 'http://localhost:9000',
        // 'idgen' => $generateId,
        // 'enable_method_path' => true,
        'enable_method_query' => true,
        'options'             => [
            'query'        => ['auth' => 'iamauth'],
            'header'       => [
                'header-you-like: value-you-mind'
            ],
            'exec_timeout' => 600,
        ]
    ]
]);

//example('user->login', function () use ($service) {
//    return $service->client('dev')->stub('user')->login('peter', '111111')->wait();
//});

example('callBatch', function () use ($service) {
    return $service->callBatch('dev', [
        ['user.login', ['user', 'pass']],
        ['user.login', ['user1', 'pass1']],
    ])->wait();
});
