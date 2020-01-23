<?php

namespace Tests\LightTracer\Trace;

use LightTracer\Plugin\HttpService;
use PHPUnit\Framework\TestCase;

class HttpServiceTest extends TestCase
{
    public function testNext()
    {
        HttpService::init(['writers' => []]);
        HttpService::logEvent('hello');
        HttpService::setTag('key', 'value');
        $this->assertEquals('value', HttpService::getTag('key'));
    }
}
