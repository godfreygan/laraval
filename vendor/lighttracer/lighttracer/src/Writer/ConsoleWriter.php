<?php

namespace LightTracer\Writer;

class ConsoleWriter extends AbstractWriter
{
    public function write($log)
    {
        echo json_encode($log, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
        return true;
    }
}
