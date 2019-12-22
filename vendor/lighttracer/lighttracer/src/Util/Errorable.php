<?php

namespace LightTracer\Util;

trait Errorable
{
    private $error = null;

    public function setError($error)
    {
        $this->error = $error;
    }

    public function triggerError($error, $error_level = E_USER_NOTICE)
    {
        if ($error) {
            \LightTracer\error_log($error, $error_level);
        }

        $this->setError($error);
    }

    public function getLastError()
    {
        return $this->error;
    }
}
