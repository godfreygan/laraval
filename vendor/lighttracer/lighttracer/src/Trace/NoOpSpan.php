<?php

namespace LightTracer\Trace;

class NoOpSpan
{
    public function __call($name, array $arguments)
    {
    }
}
