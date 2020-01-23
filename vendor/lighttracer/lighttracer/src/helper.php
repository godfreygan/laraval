<?php

namespace LightTracer;

function error_log($error, $error_level = E_USER_NOTICE)
{
    if (!is_string($error)) {
        $error = json_encode($error);
    }

    $error .= "\n";

    if (get_cfg_var('error_log')) {
        if (\error_log($error, 3, get_cfg_var('error_log'))) {
            return;
        }
    }
}
