<?php
/**
 * error response structure of LightService
 *
 * @author yuanbaoju
 */

namespace LightService\Jsonrpc\Message\Response;

class Error
{
    public $jsonrpc = '2.0';
    public $id;
    public $error;

    public function __construct()
    {
        $this->error          = new \stdClass();
        $this->error->code    = null;
        $this->error->message = null;
        // This may be omitted.
        // $this->error->data    = null;
    }
}
