<?php
/**
 * success response structure of LightService
 *
 * @author yuanbaoju
 */

namespace LightService\Jsonrpc\Message\Response;

class Success
{
    public $jsonrpc = '2.0';
    public $id;
    public $result;
}
