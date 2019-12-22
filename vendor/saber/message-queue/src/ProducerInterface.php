<?php

namespace Saber\MQ;

use Exception;
use Saber\MQ\Exception\MQException;

interface ProducerInterface
{
    /**
     * 推入一条消息
     * @param string $message
     * @param array $options
     * @throws MQException|Exception
     */
    function push($message, array $options = []): void;
}