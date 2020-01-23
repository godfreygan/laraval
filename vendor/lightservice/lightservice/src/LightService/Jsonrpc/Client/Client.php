<?php
/**
 * client for rpc
 *
 * @author yuanbaoju
 */

namespace LightService\Jsonrpc\Client;

use LightService\Results;

class Client
{
    private $call_factory;
    private $batch;

    public function __construct($call_factory)
    {
        $this->call_factory = $call_factory;
    }

    public function stub($class)
    {
        return new Stub($this, $class);
    }

    public function callBatch($calls)
    {
        $batch = $this->call_factory->callBatch();
        $batch->start();

        foreach ($calls as $call) {
            if (is_array($call)) {
                $batch->call(...$call);
            } else {
                $batch->call($call);
            }
        }

        return $batch;
    }

    public function startBatch()
    {
        ($this->batch = $this->call_factory->callBatch())->start();
    }

    public function commitBatch()
    {
        try {
            return $this->batch->wait();
        } finally {
            $this->batch = null;
        }
    }

    public function call($method, ...$args)
    {
        if (!$this->batch) {
            return $this->call_factory->call($method, $args);
        }

        $this->batch->call($method, $args);
    }

    public static function wait(...$calls)
    {
        $ret = new Results();
        $channels = [];

        foreach ($calls as $call) {
            $call->send();
            $channels[] = $call->channel;
        }

        // batchCall should be all the same type(zmq/http)
        $class = get_class($channels[0]);
        $bufs = $class::waitAll($channels);

        foreach ($bufs as $i => $buf) {
            $ret[] = $calls[$i]->push($buf);
        }

        return $ret;
    }
}
