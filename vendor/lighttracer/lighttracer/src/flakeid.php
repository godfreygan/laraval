<?php

if (!extension_loaded('flakeid')) {
    \LightTracer\error_log('flakeid.so not found!');
}

if (!function_exists('flakeid_generate')) {
    function flakeid_generate($raw_output = false, $delimiter_str = '')
    {
        $data = openssl_random_pseudo_bytes(16);
        return bin2hex($data);
    }
}

if (!function_exists('flakeid_generate64')) {
    function flakeid_generate64($raw_output = false)
    {
        $data = openssl_random_pseudo_bytes(8);
        return bin2hex($data);
    }
}

if (!function_exists('flakeid_next_seq')) {
    function flakeid_next_seq($flush_key = 0)
    {
        return rand();
    }
}

if (!function_exists('flakeid_get_mac')) {
    function flakeid_get_mac()
    {
        return substr(flakeid_generate64(false), 0, 12);
    }
}
