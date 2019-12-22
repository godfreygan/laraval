<?php

namespace Tests\LightTracer;

use PHPUnit\Framework\TestCase;

use LightTracer\GlobalTracer;

class GlobalTracerTest extends TestCase
{
    public function testScope()
    {
        GlobalTracer::init([
            'name'           =>  'GlobalTracerTest.testScope',
            'type'           =>  'phpunit',
            'trace_log_path' =>  '/tmp/lighttracer',
            'trace_log_span' =>  'day'
        ]);

        GlobalTracer::scope(function () {
            $db_span = GlobalTracer::getCurrentSpan();

            GlobalTracer::scope(function () use ($db_span) {
                $carrier = GlobalTracer::inject();
                $this->assertEquals($carrier['parent_id'], $db_span->spanId());
            }, ['check_permission', 'CHECK']);

            $this->assertEquals(GlobalTracer::spanId(), $db_span->spanId());

            GlobalTracer::scope(function () use ($db_span) {
                $carrier = GlobalTracer::inject();
                $this->assertEquals($carrier['parent_id'], $db_span->spanId());
            }, ['user_log', 'LOG']);

            $this->assertEquals(GlobalTracer::spanId(), $db_span->spanId());
        }, ['update_user_by_id', 'DB']);

        // 字符大小测试
        GlobalTracer::logEvent(str_repeat("-", 1024 * 1024)); // 1MB
        GlobalTracer::setTag('iambigdata', str_repeat("=", 1024 *1024)); // 1MB

        // 异常测试
        $scopeSpan = null;
        try {
            GlobalTracer::scope(function ($span) use(&$scopeSpan){
                $scopeSpan = $span;
                throw new \Exception('balabala');
            }, ['hello_exception', 'EXCEPTION']);
        } catch(\Exception $e) {
            $this->assertEquals(555, $scopeSpan->getTag('errno'));
        }

        try {
            GlobalTracer::scope(function ($span) use(&$scopeSpan){
                $scopeSpan = $span;
                throw new \Exception('balabala');
            }, ['hello_exception', 'EXCEPTION'], ['ignore_exception' => true]);
        } catch(\Exception $e) {
            $this->assertNull($scopeSpan->getTag('errno'));
        }
    }
}
