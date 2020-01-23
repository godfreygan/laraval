<?php
/**
 * abstract of rpc server
 *
 * @author yuanbaoju
 */

namespace LightService\Server;

abstract class AbstractServer
{
    private $loaders;
    private $methods = [];
    private $services = [];

    public function __construct($opts = [])
    {
        if (isset($opts['loaders'])) {
            $this->loaders = $opts['loaders'];
        } else {
            $this->loaders = [];
        }

        if (isset($opts['loader'])) {
            $this->registerLoader($opts['loader']);
        }
    }

    public function registerService($name, $service)
    {
        $this->services[$name] = $service;
    }

    public function registerMethod($name, $method)
    {
        $this->methods[$name] = $method;
    }

    public function registerLoader($loader)
    {
        $this->loaders[] = $loader;
    }

    private function load($service, $method)
    {
        $ret = false;

        do {
            if (isset($this->methods[$method])) {
                $ret = $this->methods[$method];
                break;
            }

            if (isset($this->services[$service])) {
                $service = $this->services[$service];
                $s = null;

                if (is_object($service)) {
                    $s = $service;
                } elseif (is_callable($service)) {
                    $s = $service();
                } else {
                    $s = new $service();
                }

                if (!method_exists($s, $method)) {
                    $ret = false;
                    break;
                }

                $ret = [$s, $method];
            }
        } while (0);

        if (false === $ret && count($this->loaders)) {
            foreach ($this->loaders as $loader) {
                if ($ret = call_user_func($loader, $service, $method)) {
                    break;
                }
            }
        }

        return $ret;
    }

    public function invoke($service, $method, ...$args)
    {
        $callable = $this->load($service, $method);

        if (is_callable($callable)) {
            return call_user_func_array($callable, $args);
        }

        throw new \BadMethodCallException();
    }

    public function invokeMethod($method, ...$args)
    {
        // if (preg_match('#(?:(?P<service>[\w|\\\\|:]+)\.)?(?P<method>.+)#', $method, $matches) <= 0) {
        // if (preg_match('#(?:/?([\w|\\\\|:|\.]+)[/\.])?([^/.]+)#', $method, $matches) <= 0) {
        if (preg_match('#(?:([\w|\\\\|:|\.]+)\.)?([^/]+)#', $method, $matches) <= 0) {
            throw new \BadMethodCallException();
        }

        return $this->invoke(empty($matches[1]) ? null : $matches[1], $matches[2], ...$args);
    }

    abstract public function respond($msg);

   // public function respondWith($reply, $id = null)
    // {
        // return $this->protocol_->encodeResponse(Response::success($reply, $id));
    // }

    // public static function on($event, $handler)
    // {
        // return EventEmitter::getInstance()->on($event, $handler);
    // }

    // public static function off($event = null, $handler = null)
    // {
        // return EventEmitter::getInstance()->off($event, $handler);
    // }

    // public static function emit()
    // {
        // return call_user_func_array(array(EventEmitter::getInstance(), 'emit'), func_get_args());
    // }
}
