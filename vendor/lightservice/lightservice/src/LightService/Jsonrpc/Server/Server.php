<?php
/**
 * jsonrpc server implementation
 *
 * @author yuanbaoju
 */

namespace LightService\Jsonrpc\Server;

use LightService\Context;
use LightService\ErrorResult;
use LightService\Jsonrpc\Constant\Status;
use LightService\Jsonrpc\Message\Response\Response;
use LightService\Jsonrpc\Protocol;
use LightService\Server\AbstractServer;

class Server extends AbstractServer
{
    private $protocol;
    private $exception_handler;

    private function defaultExceptionHandler($ex)
    {
        if ($ex instanceof \BadMethodCallException) {
            return [new ErrorResult(
                $ex->getMessage() ?: Status::METHOD_NOT_EXISTS_TRANSLATED,
                $ex->getCode() ?: Status::METHOD_NOT_EXISTS
            )];
        }

        if ($ex instanceof \InvalidArgumentException || $ex instanceof \ArgumentCountError) {
            return [new ErrorResult(
                $ex->getMessage() ?: Status::INVALID_PARAMS_TRANSLATED,
                $ex->getCode() ?: Status::INVALID_PARAMS
            )];
        }

        return [new ErrorResult(
            $ex->getMessage() ?: Status::INTERNAL_ERROR_TRANSLATED,
            $ex->getCode() ?: Status::INTERNAL_ERROR
        )];
    }

    public function __construct($opts = [])
    {
        parent::__construct($opts);

        $this->protocol = new Protocol\Jsonrpc(
            array_key_exists('use_msgpack', $opts) && $opts['use_msgpack'] ? 'msgpack' : 'json'
        );

        if (array_key_exists('exception_handler', $opts)) {
            $exception_handler = $opts['exception_handler'];
            $this->exception_handler = function ($ex) use ($exception_handler) {
                $tuple = call_user_func($exception_handler, $ex);

                if ($tuple) {
                    return array_pad($tuple, 2, null);
                }

                return array_pad($this->defaultExceptionHandler($ex), 2, null);
            };
        } else {
            $this->exception_handler = function ($ex) {
                return array_pad($this->defaultExceptionHandler($ex), 2, null);
            };
        }
    }

    private function call($req)
    {
        // $rc = preg_match('#(?:(?P<service>[\w|\\\\|:]+)\.)?(?P<method>.+)#', $req->method, $matches);
        $rc = preg_match('#(?:([\w|\\\\|:|\.]+)\.)?([^/]+)#', $req->method, $matches);

        if ($rc <= 0) {
            throw new \BadMethodCallException();
        }

        $method = null;

        if (isset($matches[2]) && !empty($matches[2])) {
            $method = $matches[2];
        } else {
            throw new \BadMethodCallException();
        }

        $service = isset($matches[1]) ? preg_replace('/\./', '\\', $matches[1]) : null;
        $params = property_exists($req, 'params') ? (array)$req->params : [];
        return $this->invoke($service, $method, ...$params);
    }

    private function dispatch($ctx, $req)
    {
        ls_emit('server.beforeDispatch', $ctx, $req, $this);
        $err = null;
        $result = null;
        $rep = null;

        try {
            $result = $this->call($req);
        } catch (\Throwable $ex) {
            list($err, $result) = call_user_func($this->exception_handler, $ex);
        } catch (\Exception $ex) {
            list($err, $result) = call_user_func($this->exception_handler, $ex);
        }

        if ($err) {
            $rep = Response::error($err);
            ls_emit('server.afterDispatch', $ctx, $req, $err, null, $this);
        } else {
            $rep = Response::success($result);
            ls_emit('server.afterDispatch', $ctx, $req, null, $result, $this);
        }

        if (property_exists($req, 'id')) {
            $rep->id = $req->id;
        }

        return $rep;
    }

    public function respond($msg)
    {
        $ctx = new Context();
        ls_emit('server.beforeHandleRequest', $ctx, $this);

        $err = null;
        $rep = null;
        $req = null;

        do {
            try {
                $req = $this->protocol->unpackRequest($msg);
            } catch (\Exception $ex) {
                list($err, $result) = call_user_func($this->exception_handler, $ex);
                break;
            }

            if (is_array($req)) {
                $rep = [];

                foreach ($req as $v) {
                    $rep[] = $this->dispatch($ctx, $v);
                }
            } else {
                $rep = $this->dispatch($ctx, $req);
            }
        } while (0);

        try {
            return $this->protocol->packResponse($err ? Response::error($err) : $rep);
        } catch (\Exception $ex) {
            list($err, $result) = call_user_func($this->exception_handler, $ex);
            return $this->protocol->packResponse($err ? Response::error($err) : Response::success($result));
        } finally {
            ls_emit('server.afterHandleRequest', $ctx, $this);
        }
    }

    public function respondLite($method, $msg)
    {
        $ctx = new Context();
        ls_emit('server.beforeHandleRequest', $ctx, $this);

        $err = null;
        $rep = null;
        $req = null;

        do {
            try {
                $req = $this->protocol->unpackRequestLite($method, $msg);
            } catch (\Exception $ex) {
                list($err, $result) = call_user_func($this->exception_handler, $ex);
                break;
            }

            $rep = $this->dispatch($ctx, $req);
        } while (0);

        try {
            return $this->protocol->packResponseLite($err ? Response::error($err) : $rep);
        } catch (\Exception $ex) {
            list($err, $result) = call_user_func($this->exception_handler, $ex);
            return $this->protocol->packResponseLite($err ? Response::error($err) : Response::success($result));
        } finally {
            ls_emit('server.afterHandleRequest', $ctx, $this);
        }
    }
}
