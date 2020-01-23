<?php
/**
 * exception for curl
 *
 * @author yuanbaoju
 */

namespace LightService\Exception;

class CurlException extends Exception
{
    public function __construct($message = 'unknown error', $code = -1, $previous = null)
    {
        parent::__construct("curl error: {$message}", $code, $previous);
    }
}
