<?php

namespace LightTracer\Writer;

use LightTracer\Util\Errorable;

abstract class AbstractWriter
{
    use Errorable;

    public function write($log)
    {
        return false;
    }
}
