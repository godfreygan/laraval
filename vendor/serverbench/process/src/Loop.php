<?php
/**
 * process loop
 * ignore noice signal, and detect term/quit signal
 */

namespace ServerBench\Process;

class Loop
{
    use Singleton;

    private $running;
    private $started;

    protected function __construct()
    {
        $this->reset();
        $this->start();

        $signal = Signal::getInstance();
        $signal->on(SIGHUP, SIG_IGN);
        $signal->on(SIGPIPE, SIG_IGN);
        $signal->on(SIGINT, [$this, 'stop']);
        $signal->on(SIGQUIT, [$this, 'stop']);
        $signal->on(SIGTERM, [$this, 'stop']);
    }

    public function running()
    {
        Signal::getInstance()->dispatch();
        return $this->running;
    }

    public function stop()
    {
        $this->running = false;
    }

    public function start()
    {
        $this->started = true;
        $this->running = true;
        return $this;
    }

    public function reset()
    {
        $this->started = false;
        $this->running = false;
        return $this;
    }

    public function __invoke()
    {
        if (!$this->started) {
            $this->start();
        }

        return $this->running();
    }
}
