<?php

namespace App\Blog\Modules\Queue;

trait KafkaSingleton
{
    public static function getInstance()
    {
        static $instance;
        if ($instance == null) {
            $instance = new static();
        }
        return $instance;
    }
}