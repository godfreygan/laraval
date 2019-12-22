<?php
class CaptchaTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $code = Service::get('base')->module('captcha')->generate();

        $this->assertGreaterThan(999, $code);
        $this->assertLessThan(10000, $code);
    }

    public function testWelcome()
    {
        $this->assertEquals(
            Service::get('base')->module('account')->welcome('ali'),
            'hello ali'
        );
    }
}
