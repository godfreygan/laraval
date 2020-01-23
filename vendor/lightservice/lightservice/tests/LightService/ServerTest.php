<?php

namespace LightService;

use LightService\Jsonrpc\Server;
use LightService\Jsonrpc\Protocol;
use LightService\Jsonrpc\Message\Request;

class ServerTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->protocol = new Protocol\Jsonrpc;
    }

    public function newServer($options = [])
    {
        return new Server\Server($options);
    }

    public function testRespond()
    {
        $server = $this->newServer();
        $server->registerMethod('hello', function () {
            return 'world';
        });

        $result = $server->respond($this->protocol->packRequest(Request::create('hello')));
        $this->assertEquals($this->protocol->unpackResponse($result)->result, 'world');
    }

    public function testRespondLite()
    {
        $server = $this->newServer();
        $server->registerMethod('hello', function () {
            return 'world';
        });

        $result = $server->respondLite('hello', $this->protocol->packRequest([]));
        $this->assertEquals($result, $this->protocol->packResponse('world'));
    }
}
