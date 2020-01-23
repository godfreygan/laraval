<?php
/**
 * shortcut helper api of lightservice
 *
 * @author yuanbaoju
 */

use LightService\Client\DefaultService;
use LightService\DefaultEventEmitter;

function ls_init($opts)
{
    return DefaultService::init($opts);
}

function ls_call($method, ...$args)
{
    return DefaultService::call($method, ...$args);
}

function ls_call_batch($service, $calls)
{
    return DefaultService::callBatch($service, $calls);
}

function ls_wait(...$calls)
{
    return $calls[0]::waitAll(...$calls);
}

function ls_emit($event, ...$args)
{
    return DefaultEventEmitter::emit($event, ...$args);
}

function ls_on($event, $listener)
{
    return DefaultEventEmitter::on($event, $listener);
}

function ls_off(...$args)
{
    return DefaultEventEmitter::off(...$args);
}
