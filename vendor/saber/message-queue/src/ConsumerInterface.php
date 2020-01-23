<?php

namespace Saber\MQ;

use Exception;
use Saber\MQ\Exception\MQException;
use Saber\MQ\Exception\TimeoutException;

interface ConsumerInterface
{
    /**
     * 弹出一条消息 处理完必须commit
     * @param int $timeout
     * @return string
     * @throws MQException|TimeoutException|Exception
     */
    function pop($timeout = 2000): ?string;

    /**
     * pop处理完毕后手动commit
     * @throws MQException|Exception
     */
    function commit(): void;
}