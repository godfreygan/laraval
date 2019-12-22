<?php

include_once __DIR__.'/../vendor/autoload.php';

use LightTracer\GlobalTracer;
use LightTracer\Writer\ZipkinWriter as ZipkinWriter;
use LightTracer\Writer\CatWriter as CatWriter;
use LightTracer\Writer\KafkaWriter as KafkaWriter;

main();

function main()
{
    LightTracer\Util\Debug::setDebug(true);

    GlobalTracer::init([
        'name'              =>  '/user/login',
        'type'              =>  'HTTP',
        'endpoint_name'     =>  'your-service',
        'endpoint_version'  =>  '1.0.0',
        'endpoint_port'     =>  80,
        'trace_sample_rate' =>  1.0,
        'writers'           => my_writers()
    ]);

    // 创建RPC用的子Span
    $span = GlobalTracer::createSpan('User.login', 'RPC');
    $span->setSideToClient();
    $span->setTag('params', ['hello', 'world']);
    $carrier = $span->inject(['request_tag' => 'beta']);

    GlobalTracer::scope(function () use ($carrier) {
        rpc($carrier);
    }, $span);
}

function rpc($carrier)
{
    // 多个tracer场景
    $tracer = LightTracer\Factory::createTracer([
        'name'              =>  'User.login',
        'type'              =>  'RPC',
        'carrier'           =>  $carrier,
        'auto_finish'       =>  false,
        'endpoint_name'     =>  'your-service',
        'endpoint_version'  =>  '1.2.0',
        'endpoint_port'     =>  80,
        'trace_sample_rate' =>  1.0,
        'writers'           => my_writers()
    ]);
    $tracer->setTag('params', ['hello', 'world']);

    // 创建子span
    $tracer->createSpan('select_user_by_name', 'DB')->scope(function ($span) {
        $span->logEvent('hello, database!');
//        throw new Exception('DB Exception');
    });

    // check username and password
    $tracer->setError(101, 'username or password not correct');

    // 结束
    $tracer->finish();
}

function my_writers()
{
//    return  [new KafkaWriter()];
     return  [new ZipkinWriter(), new CatWriter()];
}
