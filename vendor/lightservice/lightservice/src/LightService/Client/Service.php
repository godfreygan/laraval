<?php
/**
 * global service locator
 *
 * @author yuanbaoju
 */

namespace LightService\Client;

use LightService\Channel;
use LightService\Jsonrpc;
use LightService\Util\IdGen;

class Service
{
    private $opts;

    public function __construct($opts)
    {
        $this->opts = $opts;
    }

    public function client($service)
    {
        if (!isset($this->opts[$service])) {
            throw new \InvalidArgumentException("invalid service {$service}");
        }

        if (isset($this->opts[$service])) {
            $opts = $this->opts[$service];

            switch ($opts['type']) {
                case 'jsonrpc':
                    $serializer = isset($opts['serializer']) ?
                        $opts['serializer'] :
                        (isset($opts['use_msgpack']) && $opts['use_msgpack'] ? 'msgpack' : 'json');
                    $channel = null;

                    switch (isset($opts['transport']) ? $opts['transport'] : 'http') {
                        case 'http':
                            $channel = new Channel\Http($opts['url'], $opts['options'] ?: []);
                            break;
                        default:
                            $channel = call_user_func($opts['transport']);
                    }

                    return new Jsonrpc\Client\Client(new Jsonrpc\Client\Call\Factory(
                        $channel,
                        $serializer,
                        new IdGen(array_key_exists('idgen', $opts) ? $opts['idgen'] : null),
                        $opts
                    ));
            }
        }
    }

    public function stub($service, $name)
    {
        return $this->client($service)->stub($name);
    }

    public function call($method, ...$args)
    {
        if (preg_match('/((?:[\w|\-])+)::(.+)/', $method, $matches)) {
            return $this->client($matches[1])->call($matches[2], ...$args);
        }

        throw new \InvalidArgumentException("invalid method {$method}");
    }

    public function callBatch($service, $calls)
    {
        return $this->client($service)->callBatch($calls);
    }
}
