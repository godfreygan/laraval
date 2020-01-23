<?php

/**
 * php -S 127.0.0.1:9180
 * TMP_TRACE_LOG=/tmp/trace_log/trace_log.log
 * touch $TMP_TRACE_LOG
 * tail -f $TMP_TRACE_LOG
 */
include_once __DIR__ . '/../../vendor/autoload.php';

use LightTracer\GlobalTracer;
use LightTracer\Plugin\HttpService;

HttpService::init([
    'trace_log_path' => '/tmp/trace_log'
]);

try {
    echo json_encode([
        'request' => $_REQUEST,
        'server'  => $_SERVER
    ]);
} catch (\Exception $e) {
    GlobalTracer::setError(500, $e->getMessage());
}
