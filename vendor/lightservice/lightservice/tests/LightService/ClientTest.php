<?php

namespace LightService;

use LightService\Channel\HttpEcho;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    public function newTransport(...$args)
    {
        return new HttpEcho(...$args);
    }

    public function newService()
    {
        return new Client\Service([
            'test' => [
                'type' => 'jsonrpc',
                'transport' => function () {
                    return $this->newTransport('http://test');
                },
                // 'url' => 'http://localhost:7321/server.php',
                // 'idgen' => $generateId,
                // 'enable_method_path' => true,
                // 'enable_method_query' => true,
                // 'options' => [
                    // 'query' => ['auth' => 'iamauth'],
                    // 'header' => [
                        // 'header-you-like: value-you-mind'
                    // ],
                    // 'exec_timeout' => 600,
                // ]
            ]
        ]);

    }

    public function testStubCall()
    {
        $result = $this->newService()->client('test')->stub('test')->login('hello')->wait();
        $this->assertEquals($result, ['hello']);
    }

    public function testCallCall()
    {
        $result = $this->newService()->client('test')->call('hello', 'world')->wait();
        $this->assertEquals($result, ['world']);
    }

    public function testServiceCall()
    {
        $result = $this->newService()->call('test::test.hello', 'world')->wait();
        $this->assertEquals($result, ['world']);
    }

    public function testCallBatch()
    {
        $result = $this->newService()->callBatch('test', [
            ['hello1', ['world1']],
            ['hello2', ['world2']],
            ['hello3', ['world3']],
            ['hello4', ['world4']],
            ['hello5', ['world5']],
            ['hello6', ['world6']],
            ['hello7', ['world7']],
        ])->wait();

        $this->assertEquals($result->getArrayCopy(), [
            ['world1'],
            ['world2'],
            ['world3'],
            ['world4'],
            ['world5'],
            ['world6'],
            ['world7'],
        ]);
    }

    public function testCallBatch2()
    {
        $client = $this->newService()->client('test');
        $client->startBatch();
        $client->call('hello1', 'world1');
        $client->call('hello2', 'world2');
        $client->call('hello3', 'world3');
        $client->call('hello4', 'world4');
        $client->call('hello5', 'world5');
        $client->call('hello6', 'world6');
        $client->call('hello7', 'world7');
        $result = $client->commitBatch();

        $this->assertEquals($result->getArrayCopy(), [
            ['world1'],
            ['world2'],
            ['world3'],
            ['world4'],
            ['world5'],
            ['world6'],
            ['world7'],
        ]);
    }

    public function testConcurrency()
    {
        $service = $this->newService();
        $client1 = $service->client('test');
        $client2 = $service->client('test');
        $client3 = $service->client('test');
        $client4 = $service->client('test');
        $client5 = $service->client('test');

        $result = ls_wait(
            $client1->call('hello1', 'world1'),
            $client2->call('hello2', 'world2'),
            $client3->call('hello3', 'world3'),
            $client4->call('hello4', 'world4'),
            $client5->call('hello5', 'world5')
        );

        $this->assertEquals($result->getArrayCopy(), [
            ['world1'],
            ['world2'],
            ['world3'],
            ['world4'],
            ['world5'],
        ]);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testInvalidCharset()
    {
        $this->newService()->client('test')->stub('test')->hello(file_get_contents(__DIR__ . '/gbk.txt'))->wait();
    }
}
