<?php
class AccountTest extends \PHPUnit_Framework_TestCase
{
    public function testLogin()
    {
        $this->assertEquals(
            Service::get('base')->module('account')->login('ali', 'baba'),
            true
        );
    }

    public function testWelcome()
    {
        $this->assertEquals(
            Service::get('base')->module('account')->welcome('ali'),
            'hello ali'
        );
    }
}
