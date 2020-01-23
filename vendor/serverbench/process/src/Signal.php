<?php
/**
 * Signal Control Util
 */

namespace ServerBench\Process;

class Signal
{
    use Singleton;

    public function dispatch()
    {
        pcntl_signal_dispatch();
    }

    public function on($signo, $handler)
    {
        pcntl_signal($signo, $handler, false);
    }

    public function off($signo)
    {
        pcntl_signal($signo, SIG_DFL, false);
    }

    public function ignore($signo)
    {
        pcntl_signal($signo, SIG_IGN, false);
    }
}
