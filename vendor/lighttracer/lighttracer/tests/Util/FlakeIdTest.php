<?php

namespace Tests\LightTracer\Util;

use LightTracer\Util\FlakeId;
use PHPUnit\Framework\TestCase;

class FlakeIdTest extends TestCase
{
    public function testGenerate()
    {
        $this->assertEquals(32, strlen(FlakeId::generate()));
        $this->assertEquals(16, strlen(FlakeId::generate64()));

        $this->assertEquals(32, strlen(FlakeId::generateGUID()));
        $this->assertEquals(16, strlen(FlakeId::generateGUID64()));

        $this->assertEquals(12, strlen(FlakeId::getMac()));
        $this->assertEquals(8, strlen(FlakeId::getIpHex()));
        $this->assertEquals(4, count(explode('.', FlakeId::getIpv4())));
        $this->assertEquals("0a3b550c", FlakeId::getIpHex("10.59.85.12"));

        $this->assertTrue(is_int(FlakeId::nextSeq()));
    }
}
