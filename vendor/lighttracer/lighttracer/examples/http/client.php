<?php

/**
 * php examples/http/client.php  | jq ''
 */

include_once __DIR__ . '/../../vendor/autoload.php';

use LightTracer\GlobalTracer;
use LightTracer\Plugin\HttpService;

HttpService::init([
    'trace_log_path' => '/tmp/trace_log'
]);

try {
    echo HttpService::httpGetRequest('http://127.0.0.1:9180/server.php?a=1');
    echo HttpService::httpPostRequest('http://127.0.0.1:9180/server.php', ['a' => 2]);
    throw new \Exception("oh exception");
} catch (\Exception $e) {
    GlobalTracer::setError(500, $e->getMessage());
}
