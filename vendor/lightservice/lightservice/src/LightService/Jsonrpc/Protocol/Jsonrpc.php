<?php
/**
 * jsonrpc protocol
 *
 * @author yuanbaoju
 */

namespace LightService\Jsonrpc\Protocol;

use LightService\Jsonrpc\Message\Request;
use LightService\Jsonrpc\Message\Response\Response;
use LightService\Jsonrpc\Message\Response\Error;
use LightService\Util\Serializer;
use UnexpectedValueException;

class Jsonrpc
{
    private $serializer;

    public function __construct($serializer = 'json')
    {
        switch ($serializer) {
            case 'msgpack':
                $this->serializer = new Serializer\Msgpack();
                break;
            case 'json':
                $this->serializer = new Serializer\Json();
                break;
            default:
                $this->serializer = $serializer;
                break;
        }
    }

    public function packRequest($request)
    {
        return $this->serializer->serialize($request);
    }

    private function parseSingleRequest($raw)
    {
        if (!is_array($raw)) {
            throw new UnexpectedValueException("unexpected jsonrpc schama request, given {$raw}");
        }

        // strict check
        // if (!isset($raw['jsonrpc']) || $raw['jsonrpc'] != '2.0') {
            // break;
        // }

        if (!isset($raw['method'])) {
            throw new UnexpectedValueException(
                'unexpected jsonrpc schama request and method is not set, given ' . print_r($raw, true)
            );
        }

        $ret = Request::create($raw['method']);

        if (array_key_exists('id', $raw)) {
            $ret->id = $raw['id'];
        }

        if (array_key_exists('params', $raw)) {
            $ret->params = $raw['params'];
        }

        return $ret;
    }

    public function unpackRequest($buf)
    {
        $data = $this->serializer->deserialize($buf);

        if (!is_array($data)) {
            throw new UnexpectedValueException("unexpected jsonrpc schema request, given ${buf}");
        }

        $ret = null;

        if (isset($data['method'])) {
            $ret = $this->parseSingleRequest($data);
        } else {
            // may be batch
            $ret = [];

            foreach ($data as $v) {
                $ret[] = $this->parseSingleRequest($v);
            }
        }

        return $ret;
    }

    public function packResponse($response)
    {
        return $this->serializer->serialize($response);
    }

    public function parseSingleResponse($raw)
    {
        if (!is_array($raw)) {
            throw new UnexpectedValueException("unexpected jsonrpc schama response, given {$raw}");
        }

        // strict check
        // if (!isset($raw['jsonrpc']) || $raw['jsonrpc'] != '2.0') {
            // break;
        // }

        $ret = null;

        if (array_key_exists('error', $raw)) {
            $ret = Response::error($raw['error']);
        } elseif (array_key_exists('result', $raw)) {
            $ret = Response::success($raw['result']);
        } else {
            throw new UnexpectedValueException('unexpected jsonrpc schama response, given ' . print_r($raw, true));
        }

        if (array_key_exists('id', $raw)) {
            $ret->id = $raw['id'];
        }

        return $ret;
    }

    public function unpackResponse($buf)
    {
        $data = $this->serializer->deserialize($buf);

        if (!is_array($data)) {
            throw new UnexpectedValueException("unexpected jsonrpc schama response, given {$buf}");
        }

        $ret = null;

        if (array_key_exists('result', $data) || array_key_exists('error', $data)) {
            $ret = $this->parseSingleResponse($data);
        } else {
            // may be batch
            $ret = [];

            foreach ($data as $raw) {
                $ret[] = $this->parseSingleResponse($raw);
            }
        }

        return $ret;
    }

    public function packResponseLite($response)
    {
        if ($response instanceof Error) {
            return $this->serializer->serialize($response);
        }

        return $this->serializer->serialize($response->result);
    }

    public function unpackRequestLite($method, $buf)
    {
        $params = $this->serializer->deserialize($buf);

        if (is_array($params)) {
            return Request::create($method, $params);
        }

        if (empty($params)) {
            return Request::create($method);
        }

        throw new UnexpectedValueException("unexpected jsonrpc lite schama request, given {$method}({$buf})");
    }
}
