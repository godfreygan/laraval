<?php
/**
 * factory for creating call or batch in jsonrpc
 *
 * @author yuanbaoju
 */

namespace LightService\Jsonrpc\Client\Call;

use LightService\Jsonrpc\Protocol\Jsonrpc;

class Factory
{
    private $channel;
    private $protocol;
    private $idgen;
    private $opts = ['enable_method_path' => false, 'enable_method_query' => false];

    public function __construct($channel, $serializer, $idgen, $opts = [])
    {
        $this->channel = $channel;
        $this->protocol = new Jsonrpc($serializer);
        $this->idgen = $idgen;

        if (array_key_exists('enable_method_path', $opts)) {
            $this->opts['enable_method_path'] = $opts['enable_method_path'];
        }

        if (array_key_exists('enable_method_query', $opts)) {
            $this->opts['enable_method_query'] = true === $opts['enable_method_query'] ?
                '__rpc' : $opts['enable_method_query'];
        }
    }

    public function call($method, $args)
    {
        return new Call($this->channel, $this->protocol, $method, $args, $this->idgen, $this->opts);
    }

    public function callBatch()
    {
        return new Batch($this->channel, $this->protocol, $this->idgen, $this->opts);
    }
}
