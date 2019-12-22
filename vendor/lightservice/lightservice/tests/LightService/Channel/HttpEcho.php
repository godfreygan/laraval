<?php

namespace LightService\Channel;

use LightService\Channel\Http;
use LightService\Jsonrpc\Protocol;
use LightService\Jsonrpc\Message\Response\Response;

class HttpEcho extends Http
{
    private $stash = null;
    private $protocol = null;

    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $this->protocol = new Protocol\Jsonrpc;
    }

    public function send($message, $opts = [])
    {
        $request = $this->protocol->unpackRequest($message);


        if (is_array($request)) {
            $this->stash = $this->protocol->packResponse(array_map(function ($request) {
                return Response::success($request->params);
            }, $request));
        } else {
            $this->stash = $this->protocol->packResponse(Response::success($request->params));
        }

        return $this;
    }

    public function wait()
    {
        return $this->stash;
    }

    public static function waitAll($channels)
    {
        $ret = [];

        foreach ($channels as $channel) {
            $ret[] = $channel->wait();
        }

        return $ret;
    }
}
