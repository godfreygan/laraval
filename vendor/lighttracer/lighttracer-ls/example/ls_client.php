<?php

require '../vendor/autoload.php';

use LightService\Client\Service;
use LightService\ErrorResult;
use LightService\Results;

LightTracer\Plugin\LightService::init([
    'endpoint_name'  => 'api.s0.com',
    'trace_log_path' => 'd:/trace_log/s0',
    'trace_log_span' => 'day',
]);

$service = new Service([
    'dev' => [
        'type'                => 'jsonrpc',
        'url'                 => 'http://localhost:9001', // userService
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

example('user->login', function () use ($service) {
    return $service->client('dev')->stub('user')->login('peter', '111111')->wait();
});

//example('callBatch', function () use ($service) {
//    return $service->callBatch('dev', [
//        ['user.login', ['user', 'pass']],
//        ['user.login', ['user1', 'pass1']],
//    ])->wait();
//});
