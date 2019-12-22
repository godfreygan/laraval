<?php

namespace Tests\LightTracer\Trace;

use LightTracer\Trace\EndPoint;
use PHPUnit\Framework\TestCase;

class EndPointTest extends TestCase
{
    public function testInitGlobalEndPoint()
    {
        $endpoint = new EndPoint("abc", "1.0.1", "192.168.1.10", 110, 100);

        $this->assertEquals("abc", $endpoint->getServiceName());
        $this->assertEquals("1.0.1", $endpoint->getVersion());
        $this->assertEquals("192.168.1.10", $endpoint->getIpv4());
        $this->assertEquals(110, $endpoint->getPort());
        $this->assertEquals(100, $endpoint->getPid());
    }
}
