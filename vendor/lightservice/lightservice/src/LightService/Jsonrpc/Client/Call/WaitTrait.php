<?php

namespace LightService\Jsonrpc\Client\Call;

use LightService\Results;

trait WaitTrait {
    public static function waitAll(...$calls)
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
