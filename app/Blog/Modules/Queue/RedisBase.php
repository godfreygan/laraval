<?php

namespace App\Blog\Modules\Queue;


abstract class RedisBase
{
    //发送队列
    abstract public function send($message);

    //处理队列
    abstract public function receive();

}