<?php
/**
 * call for jsonrpc
 *
 * @author yuanbaoju
 */

namespace LightService\Jsonrpc\Client\Call;

use LightService\Context;
use LightService\ErrorResult;
use LightService\Exception\Exception;
use LightService\Jsonrpc\Constant\Status;
use LightService\Jsonrpc\Message\Request;
use LightService\Jsonrpc\Message\Response\Error;

class Call
{
    use WaitTrait;

    public $method;
    public $params;
    public $channel;
    private $ctx;
    private $protocol;
    private $rep;
    private $idgen;
    private $enable_method_path;
    private $enable_method_query;
    private $finished = false;

    public function __construct($channel, $protocol, $method, $params, $idgen, $opts = [])
    {
        $this->channel = $channel;
        $this->protocol = $protocol;
        $this->method = $method;
        $this->params = $params;
        $this->idgen = $idgen;

        if (array_key_exists('enable_method_path', $opts)) {
            $this->enable_method_path = $opts['enable_method_path'];
        }

        if (array_key_exists('enable_method_query', $opts)) {
            $this->enable_method_query =  $opts['enable_method_query'];
        }
    }

    public function response()
    {
        return $this->rep;
    }

    public function push($buf)
    {
        if ($buf instanceof \Exception) {
            $this->rep = ErrorResult::fromException($buf);
            ls_emit('client.afterCall', $this->ctx, $this, $this->rep, null);
            $this->finished = true;
            return $this->rep;
        }

        try {
            $rep = $this->protocol->unpackResponse($buf);

            if ($rep instanceof Error) {
                throw new Exception($rep->error->message, $rep->error->code);
            }

            $this->rep = $rep->result;
            ls_emit('client.afterCall', $this->ctx, $this, null, $this->rep);
        } catch (\Exception $ex) {
            $this->rep = ErrorResult::fromException($ex);
            ls_emit('client.afterCall', $this->ctx, $this, $this->rep, null);
        } finally {
            $this->finished = true;
        }

        return $this->rep;
    }

    public function send()
    {
        $this->ctx = new Context();
        ls_emit('client.beforeCall', $this->ctx, $this);

        $opts = [];

        if ($this->enable_method_path) {
            $opts['path'] = urlencode($this->method);
        }

        if ($this->enable_method_query) {
            $opts['query'] = [$this->enable_method_query => $this->method];
        }

        return $this->channel->send(
            $this->protocol->packRequest(
                Request::create($this->method, $this->params, call_user_func($this->idgen))
            ),
            $opts
        );
    }

    public function wait(&$out = null)
    {
        try {
            $buf = $this->send()->wait();
            $rep = $this->protocol->unpackResponse($buf);

            if ($rep instanceof Error) {
                throw new Exception(
                    $rep->error->message ? $rep->error->message : Status::translate($rep->error->code),
                    $rep->error->code
                );
            }

            $this->rep = $rep->result;
            ls_emit('client.afterCall', $this->ctx, $this, null, $this->rep);

            return $this->rep;
        } catch (\Exception $ex) {
            $this->rep = ErrorResult::fromException($ex);
            ls_emit('client.afterCall', $this->ctx, $this, $this->rep, null);

            if (func_num_args()) {
                $out = $this->rep;
            } else {
                throw $ex;
            }
        } finally {
            $this->finished = true;
        }
    }

    public function __toString()
    {
        return "call({$this->method})";
    }

    public function __destruct()
    {
        if (!$this->finished) {
            trigger_error("{$this} is not finished", E_USER_NOTICE);
        }
    }
}
