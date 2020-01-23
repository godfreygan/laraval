<?php

namespace Tests\LightTracer\Trace;

use LightTracer\Trace\Tracer as Tracer;
use LightTracer\Trace\Span as Span;
use LightTracer\Trace\EndPoint as EndPoint;
use LightTracer\Sampler\RandSampler as RandSampler;
use LightTracer\Util\FlakeId;
use LightTracer\Util\Debug as DebugUtil;
use LightTracer\Writer\ZipkinWriter as ZipkinWriter;
use LightTracer\Writer\ConsoleWriter as ConsoleWriter;
use LightTracer\Writer\FileWriter as FileWriter;
use LightTracer\Writer\CatWriter as CatWriter;
use LightTracer\Writer\ESWriter as ESWriter;
use PHPUnit\Framework\TestCase;

class TracerTest extends TestCase
{
    public function testSampled()
    {
        $this->runTracing(1.0);
    }

    public function testNotSampled()
    {
        $this->runTracing(0.0);
    }

    public function runTracing($rate)
    {
        $_SERVER['REQUEST_URI'] = '/user/login';
        $endpoint               = new EndPoint('com.lighttracer.service.user', '1.0.0', FlakeId::getIpv4(), 80, getmypid());
        $sampler                = new RandSampler($rate);

        // writers
        $writers   = [];
        $writers[] = new FileWriter('trace_log', '/tmp/trace_log', 86400, 'log', false);
        $writers[] = new ZipkinWriter();
        $writers[] = new CatWriter();
//        $writers[] = new ESWriter();


        ##################
        # 启动阶段
        ##################

        $tracer = new Tracer([
            'name'     => $_SERVER['REQUEST_URI'],
            'type'     => 'HTTP',
            'sampled'  => $sampler->shouldSample(),
            'endpoint' => $endpoint,
            'writers'  => $writers,
            'carrier'  => [],
        ]);

        if (array_key_exists('HTTP_TRACE_ID', $_ENV)) {
            $tracer['carrier']['trace_id'] = $_ENV['HTTP_TRACE_ID'];
        }

        $tracer->start();

        ##################
        # 运行阶段
        ##################

        // 普通打点
        $tracer->logEvent("concat");
        usleep(10);

        $tracer->logEvent("sum");
        usleep(10);

        $tracer->setTag("errno", 0);

        // 生成缩略图
        $span = $tracer->createSpan('create_thumbnails', 'IMAGE');
        $span->start();

        usleep(1000);
        $span->logEvent('IMAGE_SIZE_640_640 created');

        usleep(1000);
        $span->logEvent('IMAGE_SIZE_100_100 created');

        $span->finish();

        ##################
        # 结束阶段
        ##################
        $tracer->finish();


        // 单元测试用
        if ($tracer->sampled()) {
            $trace_id = $tracer->traceId();
            $this->assertEquals(32, strlen($trace_id));
            DebugUtil::log("TracerTest traceId = $trace_id finish");
        } else {
            DebugUtil::log("TracerTest no sampled");
        }
    }
}
