<?php

namespace Tests\LightTracer\Trace;

use LightTracer\Trace\Span as Span;
use LightTracer\Trace\Tracer as Tracer;
use LightTracer\Trace\EndPoint as EndPoint;
use LightTracer\Util\FlakeId;
use LightTracer\Util\Debug as DebugUtil;
use LightTracer\Writer\ZipkinWriter as ZipkinWriter;
use LightTracer\Writer\ConsoleWriter as ConsoleWriter;
use LightTracer\Writer\FileWriter as FileWriter;
use LightTracer\Writer\CatWriter as CatWriter;
use LightTracer\Writer\ESWriter as ESWriter;
use PHPUnit\Framework\TestCase;

class SpanLogTest extends TestCase
{
    private $log_path;

    public function testRPC()
    {
        /* RPC 客户端样例 */
        $trace_id = Tracer::generateTraceId();
        $this->assertEquals(32, strlen($trace_id));

        // root span start
        $root_span = new Span('/user/login', 'HTTP');
        $root_span->traceId($trace_id);
        $root_span->endpoint(new EndPoint('com.lighttracer.app', '1.0.1', FlakeId::getIpv4(), 80));
        $root_span->writer($this->getWriter());
        $root_span->start();

        // request span start
        $span = new Span('User.login', 'RPC');
        $span->setParentSpan($root_span);
        $span->side(Span::SPAN_SIDE_CLIENT);
        $span->setTag('params', ['hello', 'world']);
        $span->start();

        $carrier = $span->inject(['request_tag' => 'beta']);
        $this->callRPC($carrier);

        // request span finish
        $this->assertTrue($span->finish());

        // root span finish
        $this->assertTrue($root_span->finish());

        // 模拟日志传输
        $this->transferLog();

        DebugUtil::log("traceId = {$trace_id} finished");
    }

    protected function callRPC($carrier)
    {
        /* RPC 服务器端样例 */
        if ($carrier['trace_id']) {
            $span = new Span();
            $span->extract($carrier);
            $span->endpoint(new EndPoint('com.lighttracer.service', '1.0.0', '192.168.1.20', 8080));
            $span->writer($this->getWriter());
            $span->side(Span::SPAN_SIDE_SERVER);
            $span->setTag('params', ['hello', 'world']);

            $this->assertEquals('beta', $span->getBaggage()['request_tag']);

            // start
            $span->start();

            // handle
            usleep(100);
            $span->logEvent('read from db');

            usleep(100);
            $span->logEvent('check username and password');

            usleep(1000);
            $span->logEvent('write login log');
            usleep(100);

            // 模拟成功或出错
            $errno  = rand(0, 1);
            $errstr = 'lalala';

            // finish
            $span->setTag('errno', $errno);
            $span->setTag('errstr', $errstr);
            $this->assertTrue($span->finish());
        }
    }

    protected function getWriter()
    {
        $writers = [];

        $DIR = '/tmp/';

        // file
        $writer         = new FileWriter('trace_test', $DIR, false, 'log', false);
        $this->log_path = $DIR . $writer->getCurrentName();

        if (file_exists($this->log_path)) {
            unlink($this->log_path);
        }

        $writers[] = $writer;
        return $writers;
    }

    private function transferLog()
    {
        $writers = [];

        // zipkin
        $writers[] = new ZipkinWriter();

        // cat
        $writers[] = new CatWriter();

        // Elastic Stack
//        $writers[] = new ESWriter();

        // console
//        $writers[] = new ConsoleWriter();

//        echo "\nread log file " . $this->log_path . "\n";
        $file_handler = fopen($this->log_path, 'r');
        while ($line = fgets($file_handler)) {
            $log = json_decode($line, true);
            foreach ($writers as $writer) {
                $this->assertTrue($writer->write($log));
            }
        }
    }
}
