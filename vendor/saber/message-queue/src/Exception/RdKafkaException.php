<?php

namespace Saber\MQ\Exception;

use Throwable;

class RdKafkaException extends MQException
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}