<?php
/**
 * exception for jsonrpc
 *
 * @author yuanbaoju
 */

namespace LightService\Jsonrpc\Exception;

use LightService\Jsonrpc\Constant\Status;

class JsonrpcException extends \RuntimeException
{
    public function __construct($code, $message = null)
    {
        parent::__construct($message ?: Status::translate($code), $code);
    }
}
