<?php

require __DIR__ . '/../../../vendor/autoload.php';

use LightService\Client\Service;
use LightService\Util\IdGen;
use LightService\ErrorResult;
use LightService\Results;

ls_on('client.beforeCall', function ($event, $ctx, $call) {
    echo "$call\n";
    $ctx->starttime = microtime(true);
});

ls_on('client.afterCall', function ($event, $ctx, $call, $err, $result) {
    $endtime = microtime(true);
    $delta = ($endtime - $ctx->starttime) * 1000;

    echo "time used: $delta ms\n";
    // if ($err) {

    // } else {
        // if ($result instanceof Results) {
            // foreach ($result as $i => $v) {
                // echo "#$i ", print_r($v, true), "\n";
            // }
        // } else {
            // print_r($result);
        // }

    // }
});

function example($name, $case) {
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
        'type' => 'jsonrpc',
        'url' => 'http://localhost:7321/server.php',
        // 'idgen' => $generateId,
        // 'enable_method_path' => true,
        'enable_method_query' => true,
        'options' => [
            'query' => ['auth' => 'iamauth'],
            'header' => [
                'header-you-like: value-you-mind'
            ],
            'exec_timeout' => 600,
        ]
    ]
]);

// get specified module
example('user->login', function () use ($service) {
    return $service->client('dev')->stub('user')->login('foo', 'bar', file_get_contents(__DIR__ . '/gbk.txt'))->wait();
});

example('Echo->hi', function() use($service) {
    return $service->client('dev')->stub('Echo')->hi('haode')->wait();
});

example('call', function() use ($service) {
    return $service->call('dev::hashit.hash', 'data')->wait();
});

example('callBatch', function() use ($service) {
    return $service->callBatch('dev', [
        ['hashit.hash', ['data1']],
        ['hashit.hash', [file_get_contents(__DIR__ . '/gbk.txt')]],
        ['hashit.hash', ['data3']],
        ['hashit.hash', ['data4']],
        ['hashit.hash', ['data5']],
        ['hashit.hash', ['data6']],
        ['hashit.hash', [file_get_contents(__DIR__ . '/gbk.txt')]]
    ])->wait();
});

example('callBatch2', function() use($service) {
    $client = $service->client('dev');
    $client->startBatch();
    $client->call('hashit.hash', 'data1');
    $client->call('hashit.hash', 'data2');
    $client->call('hashit.hash', 'data3');
    $client->call('hashit.hash', 'data4');
    $client->call('hashit.hash', 'data5');
    return $client->commitBatch();
});

example('batchCall', function() use($service) {
    return $service->callBatch('dev', [
        ['DataCenter.getCityList', 1],
        ['DataCenter.getUserList', 2],
        ['DataCenter.getNoneList', 3],
        ['DataCenter.getIpList',   4],
        ['DataCenter.getAnyThingWithError', 5]
    ])->wait();
});

example('concurrency', function() use($service) {
    $client1 = $service->client('dev');
    $client2 = $service->client('dev');
    $client3 = $service->client('dev');
    $client4 = $service->client('dev');

    return ls_wait(
        $client1->call('DataCenter.getCityList', 1),
        $client2->call('DataCenter.getUserList', 2),
        $client3->call('DataCenter.getNoneList', 3),
        $client4->call('DataCenter.getIpList', 4)
    );
});
