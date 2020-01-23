<?php
/**
 * stub for any call
 *
 * @author yuanbaoju
 */

namespace LightService\Jsonrpc\Client;

class Stub
{
    private $client;
    private $class;

    public function __construct($client, $class)
    {
        $this->client = $client;
        $this->class = $class;
    }

    public function __call($method, $args = [])
    {
        return $this->client->call("{$this->class}.{$method}", ...$args);
    }
}
