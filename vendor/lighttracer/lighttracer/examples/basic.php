<?php

use LightTracer\GlobalTracer;

include_once __DIR__ . '/../vendor/autoload.php';

/**
 * 方法一：配置通过参数传入
 */
GlobalTracer::init([
    'name'              => 'basic.php',
    'type'              => 'SCRIPT',
    'endpoint_name'     => 'your-service',
    'endpoint_version'  => '1.0.0',
    'endpoint_port'     => 80,
    'trace_sample_rate' => 1.0,
    'trace_log_path'    => '/tmp/lighttracer',
    'trace_log_span'    => 86400
]);

/**
 * 方法二：配置写在php.ini中
 * php.ini
 * endpoint_name=com.lighttracer
 * endpoint_ipv4=202.202.202.202
 * endpoint_port=80
 * endpoint_version=1.0.0
 * trace_sample_rate=1.0
 * trace_log_path=/tmp/lighttracer
 * trace_log_span=86400
 *
 * GlobalTracer::init();
 */
// 获取当前trace_id
$trace_id = GlobalTracer::traceId();
GlobalTracer::logEvent('current trace id ' . $trace_id);

// 事件
GlobalTracer::logEvent('hello world');

// 标签
GlobalTracer::setTag('hello', 'world');

// 错误
GlobalTracer::setError(100, 'hello world');

// 大数据测试
for ($i = 0; $i < 1; $i++) {
    GlobalTracer::setTag("bigdata_$i", str_repeat("=", 1024 * 1024)); // 1M
}
