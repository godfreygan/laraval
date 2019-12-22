<?php

namespace LightTracer\Plugin;

use LightTracer\GlobalTracer;

class HttpService
{
    public static function init($conf = [])
    {
        $has_http_carrier = self::hasCarrierInHttpHeader();
        $default          = [];

        if ($has_http_carrier) {
            $default['carrier'] = self::getCarrierFromHttpHeader();
        } elseif (array_key_exists('REQUEST_URI', $_SERVER)) {
            $default['name'] = explode('?', $_SERVER['REQUEST_URI'])[0];
            $default['type'] = PHP_SAPI;
        } elseif (count($_SERVER['argv'])) {
            $default['name'] = $_SERVER['argv'][0];
            $default['type'] = PHP_SAPI;
        }

        $conf   = array_merge($default, $conf);
        $tracer = GlobalTracer::init($conf);

        return $tracer;
    }

    public static function httpRequest($url, $curl_options = [])
    {
        $method = array_key_exists(CURLOPT_CUSTOMREQUEST, $curl_options) ? $curl_options[CURLOPT_CUSTOMREQUEST] : 'GET';
        $span   = GlobalTracer::createSpan($method, 'CURL');
        $span->setTag('request_url', $url);
        $span->setSideToClient();
        GlobalTracer::startSpan($span);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // 设置Trace传给下游的信息
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::generateCarrierHttpHeader());

        // 设置额外的选项
        foreach ($curl_options as $key => $value) {
            curl_setopt($ch, $key, $value);
        }

        $response = curl_exec($ch)
        ;
        if (curl_errno($ch)) {
            GlobalTracer::setError(curl_errno($ch), curl_error($ch) . ": $url");
        } else {
            GlobalTracer::setTag('response', $response);
        }

        curl_close($ch);
        GlobalTracer::finishSpan($span);
        return $response;
    }

    public static function httpGetRequest($url)
    {
        return self::httpRequest($url, [
            CURLOPT_CUSTOMREQUEST => 'GET'
        ]);
    }

    public static function httpPostRequest($url, $posts = '')
    {
        return self::httpRequest($url, [
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS    => $posts
        ]);
    }

    public static function generateCarrierHttpHeader($span = null)
    {
        if (is_null($span)) {
            $span = GlobalTracer::getCurrentSpan();
        }

        $carrier = $span->inject();
        $fields  = [];
        foreach ($carrier as $key => $value) {
            $key      = 'x-' . str_replace('_', '-', $key);
            $fields[] = "$key: $value";
        }

        return $fields;
    }

    private static function hasCarrierInHttpHeader()
    {
        return array_key_exists('HTTP_X_TRACE_ID', $_SERVER);
    }

    private static function getCarrierFromHttpHeader()
    {
        $carrier = [];
        $fields  = ['trace_id', 'span_id', 'parent_id', 'operation_name', 'operation_type'];

        foreach ($fields as $field) {
            $key = 'HTTP_X_' . strtoupper($field);
            if (array_key_exists($key, $_SERVER)) {
                $carrier[$field] = $_SERVER[$key];
            }
        }

        return $carrier;
    }

    public static function __callStatic($name, array $arguments)
    {
        return call_user_func_array('\LightTracer\GlobalTracer::' . $name, $arguments);
    }
}
