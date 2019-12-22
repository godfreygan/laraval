<?php

namespace LightTracer\Trace;

use LightTracer\Util\FlakeId as FlakeId;

class Tracer
{
    private $span_stack = [];
    private $root_span = null;
    private $sampled = null;
    private $conf;

    public function __construct($conf = [])
    {
        if (!array_key_exists('type', $conf)) {
            $conf['type'] = PHP_SAPI;
        }

        $this->conf = $conf;
        $carrier = array_key_exists('carrier', $conf) ? $conf['carrier'] : [];

        if (array_key_exists('trace_id', $carrier) && $carrier['trace_id']) {
            $span = $this->getRootSpan();
            $span->extract($carrier);
            $span->setSideToServer();
        }
    }

    public function sampled()
    {
        if (is_null($this->sampled)) {
            if (array_key_exists('sampled', $this->conf)) {
                $this->sampled = $this->conf['sampled'];
            } else {
                $this->sampled = true;
            }
        }

        return $this->sampled;
    }

    public function getRootSpan()
    {
        if ($this->root_span) {
            return $this->root_span;
        }

        if (!$this->sampled()) {
            $this->root_span = new NoOpSpan();
            return $this->root_span;
        }

        $conf = $this->conf;

        $span = new Span($conf['name'], $conf['type']);

        $trace_id = self::generateTraceId();
        $span->traceId($trace_id);

        if (array_key_exists('endpoint', $conf)) {
            $span->endpoint($conf['endpoint']);
        }

        if (array_key_exists('writers', $conf)) {
            $span->writer($conf['writers']);
        }

        $this->root_span = $span;
        return $this->root_span;
    }

    public function createSpan($operation_name = 'unknown', $operation_type = '', $options = [])
    {
        if (!$this->sampled()) {
            return new NoOpSpan();
        }

        if (array_key_exists('parent_span', $options)) {
            $parent_span = $options['parent_span'];
        } else {
            $parent_span = $this->getCurrentSpan();
        }

        $span = new Span($operation_name, $operation_type);
        $span->setParentSpan($parent_span);

        if (array_key_exists('side', $options)) {
            $span->side($options['side']);
        }

        return $span;
    }

    public function startSpan($span, $us = null)
    {
        $this->pushCurrentSpan($span);
        $span->start($us);
    }

    public function finishSpan($span, $us = null)
    {
        $this->popCurrentSpan();
        $span->finish($us);
    }

    public function scope($func, $span_params = [], $options = [])
    {
        if (is_array($span_params)) {
            $span = call_user_func_array([$this, 'createSpan'], $span_params);
        } else {
            $span = $span_params;
        }

        $this->pushCurrentSpan($span);

        $result = $span->scope($func, $options);

        $this->popCurrentSpan();

        return $result;
    }

    public function __call($name, array $arguments)
    {
        return call_user_func_array([$this->getCurrentSpan(), $name], $arguments);
    }

    public function getCurrentSpan()
    {
        $count = count($this->span_stack);
        return $count ? $this->span_stack[$count - 1] : $this->getRootSpan();
    }

    public function pushCurrentSpan($span)
    {
        array_push($this->span_stack, $span);
    }

    public function popCurrentSpan()
    {
        return count($this->span_stack) ? array_pop($this->span_stack) : null;
    }

    public static function generateTraceId()
    {
        return FlakeId::generate();
    }
}
