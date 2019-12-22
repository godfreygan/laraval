<?php

namespace Tests\LightTracer\Trace;

use LightTracer\Sampler\RandSampler as RandSampler;
use PHPUnit\Framework\TestCase;

class RandSamplerTest extends TestCase
{
    public function testNext()
    {
        $sampler = new RandSampler(1.0);
        for ($i = 0; $i < 100; $i++) {
            $this->assertTrue($sampler->shouldSample());
        }

        $sampler->rate = 0;
        for ($i = 0; $i < 100; $i++) {
            $this->assertFalse($sampler->shouldSample());
        }

        $sampler->rate = 0.1;
        $count         = 0;
        for ($i = 0; $i < 1000; $i++) {
            if ($sampler->shouldSample()) {
                $count++;
            }
        }
        $this->assertTrue($count > 20 && $count < 180);
    }
}
