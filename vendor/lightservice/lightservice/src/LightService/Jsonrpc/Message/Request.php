<?php
/**
 * request structure of LightService
 *
 * @author yuanbaoju
 */

namespace LightService\Jsonrpc\Message;

class Request
{
    public $jsonrpc = '2.0';

    // If it is not included it is assumed to be a notification.
    // public $id;
    // This member MAY be omitted.
    // public $params;
    public $method;

    public static function create($method, $params = null, $id = null)
    {
        $req = new static();
        $req->method = $method;

        if (isset($params)) {
            $req->params = $params;
        }

        if (isset($id)) {
            $req->id = $id;
        }

        return $req;
    }
}
