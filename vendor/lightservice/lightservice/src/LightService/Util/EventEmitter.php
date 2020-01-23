<?php
/**
 * event emitter for subscribing or publishing event
 *
 * @author yuanbaoju
 */

namespace LightService\Util;

class EventEmitter
{
    protected $listeners = [];

    public function emit($event, ...$args)
    {
        if (array_key_exists($event, $this->listeners)) {
            foreach ($this->listeners[$event] as $listener) {
                call_user_func($listener, $event, ...$args);
            }
        }
    }

    public function on($event, $listener)
    {
        if (!array_key_exists($event, $this->listeners)) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;
    }

    public function off($event = null, $listener = null)
    {
        if ($event) {
            if ($listener) {
                if (isset($this->listeners[$event])) {
                    foreach (array_keys($this->listeners[$event], $listener, true) as $key) {
                        unset($this->listeners[$key]);
                    }
                }
            } else {
                unset($this->listeners[$event]);
            }
        } else {
            $this->listeners = [];
        }
    }
}
