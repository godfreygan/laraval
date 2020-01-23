<?php
/**
 * channel interface of LightService
 *
 * @author yuanbaoju
 */

namespace LightService\Channel;

abstract class Channel
{
    abstract public function send($message);
    abstract public function wait();
    abstract public static function waitAll($channels);
}
