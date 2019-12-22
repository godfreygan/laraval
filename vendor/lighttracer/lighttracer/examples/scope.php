<?php

include_once __DIR__.'/../vendor/autoload.php';

use LightTracer\GlobalTracer;
use LightTracer\Writer\ZipkinWriter as ZipkinWriter;
use LightTracer\Writer\KafkaWriter as KafkaWriter;

LightTracer\Util\Debug::setDebug(true);

GlobalTracer::init([
    'name'    =>  'examples.scope',
    'type'    =>  'EXAMPLE',
//    'writers' =>  [new KafkaWriter()]
    'writers' =>  [new ZipkinWriter()]
]);

GlobalTracer::scope(function () {
    GlobalTracer::logEvent('start update user');

    GlobalTracer::scope(function () {
        GlobalTracer::logEvent('permission ok');
    }, ['check_permission', 'CHECK']);

    GlobalTracer::scope(function () {
        GlobalTracer::logEvent('user log inserted');
    }, ['user_log', 'LOG']);

    GlobalTracer::logEvent('user updated');

    $span = GlobalTracer::createSpan('something', 'OTHER');
    GlobalTracer::startSpan($span);
    usleep(30 * 1000);
    GlobalTracer::finishSpan($span);

}, ['update_user_by_id', 'DB']);

GlobalTracer::scope(function ($span) {
    usleep(30 * 1000);
    $span->logEvent('i am heavy!');
}, ['heavy_task', 'TASK']);

GlobalTracer::scope(function ($span) {
    $ch = curl_init('http://www.baidu.com');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    $span->setTag('code', $info['http_code']);
    $span->setTag('response', $response);
}, ['baidu.com', 'CURL']);

GlobalTracer::scope(function ($span) {
    $ch = curl_init('http://www.google.com');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    $span->setTag('code', $info['http_code']);
    $span->setTag('response', $response);
}, ['google.com', 'CURL']);
