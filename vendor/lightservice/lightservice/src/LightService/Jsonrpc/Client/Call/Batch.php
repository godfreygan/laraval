<?php
/**
 * batch call for jsonrpc
 *
 * @author yuanbaoju
 */

namespace LightService\Jsonrpc\Client\Call;

use LightService\Context;
use LightService\ErrorResult;
use LightService\Exception\Exception;
use LightService\Jsonrpc\Message\Request;
use LightService\Jsonrpc\Message\Response\Error;
use LightService\Results;

class Batch
{
    use WaitTrait;

    public $calls;
    public $channel;
    private $protocol;
    private $idgen;
    private $rep;
    private $enable_method_path;
    private $enable_method_query = null;
    private $ctx;
    private $finished = false;

    public function __construct($channel, $protocol, $idgen, $opts = [])
    {
        $this->channel = $channel;
        $this->protocol = $protocol;
        $this->idgen = $idgen;
        $this->enable_method_path = array_key_exists('enable_method_path', $opts) && $opts['enable_method_path'];

        if (array_key_exists('enable_method_query', $opts)) {
            $this->enable_method_query =  $opts['enable_method_query'];
        }
    }

    public function start()
    {
        $this->calls = [];
    }

    public function call($method, $args)
    {
        $this->calls[] = Request::create($method, $args, call_user_func($this->idgen));
    }

    private function concatMethodsStr()
    {
        return implode(' ', array_map(function ($item) {
            return $item->method;
        }, $this->calls));
    }

    public function send()
    {
        $this->ctx = new Context();
        ls_emit('client.beforeCall', $this->ctx, $this);

        $opts = [];

        if ($this->enable_method_path) {
            $opts['path'] = $this->concatMethodsStr();
        }

        if ($this->enable_method_query) {
            $opts['query'] = [
                $this->enable_method_query => $this->enable_method_path ? $opts['path'] : $this->concatMethodsStr()
            ];
        }

        return $this->channel->send($this->protocol->packRequest($this->calls), $opts);
    }

    public function wait(&$out = null)
    {
        try {
            $buf = $this->send()->wait();
            $rep = $this->protocol->unpackResponse($buf);

            if (is_array($rep)) {
                $this->rep = new Results();

                foreach ($rep as $v) {
                    $this->rep[] = $v instanceof Error ?
                        new ErrorResult($v->error->message, $v->error->code) : $v->result;
                }

                ls_emit('client.afterCall', $this->ctx, $this, null, $this->rep);
                return $this->rep;
            } elseif ($rep instanceof Error) {
                throw new Exception($rep->error->message, $rep->error->code);
            } else {
                throw new Exception("invalid response {$buf}", -1);
            }
        } catch (\Exception $ex) {
            $this->rep = ErrorResult::fromException($ex);
            ls_emit('client.afterCall', $this->ctx, $this, null, $this->rep);

            if (func_num_args()) {
                $out = $this->rep;
            } else {
                throw $ex;
            }
        } finally {
            $this->finished = true;
        }
    }

    public function responses()
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

            if (is_array($rep)) {
                $this->rep = new Results();

                foreach ($rep as $v) {
                    $this->rep[] = $v instanceof Error ?
                        new ErrorResult($v->error->message, $v->error->code) : $v->result;
                }

                ls_emit('client.afterCall', $this->ctx, $this, null, $this->rep);
            } elseif ($rep instanceof Error) {
                throw new Exception($rep->error->message, $rep->error->code);
            } else {
                throw new Exception("Invalid response {$buf}", -1);
            }
        } catch (\Exception $ex) {
            $this->rep = ErrorResult::fromException($ex);
            ls_emit('client.afterCall', $this->ctx, $this, $this->rep, null);
        } finally {
            $this->finished = true;
        }

        return $this->rep;
    }

    public function __toString()
    {
        $str = implode(', ', array_map(function ($item) {
            return $item->method;
        }, $this->calls));

        return "batch({$str})";
    }

    public function __destruct()
    {
        if (!$this->finished) {
            trigger_error("{$this} is not finished", E_USER_NOTICE);
        }
    }
}
