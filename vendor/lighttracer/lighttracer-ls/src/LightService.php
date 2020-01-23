<?php

namespace LightTracer\Plugin;

use LightTracer\GlobalTracer;

class LightService extends HttpService
{
    public static function init($conf = [])
    {
        $tracer = parent::init($conf);
        self::setHook();

        return $tracer;
    }

    private static function setHook()
    {
        ls_on('client.beforeCall', function ($event, $ctx, $call) {
            $is_batch = false;
            if (isset($call->calls) && is_array($call->calls)) {
                $is_batch = true;
            }

            if ($is_batch) {
                $span_name = $call;
                $span      = GlobalTracer::createSpan($span_name, 'BATCH_RPC');
            } else {
                $span_name = $call->method;
                $span      = GlobalTracer::createSpan($span_name, 'RPC');

                if (property_exists($call, 'params')) {
                    $span->setTag('request_params', $call->params);
                }
            }

            $span->setSideToClient();
            GlobalTracer::startSpan($span);

            $header  = [];
            $carrier = $span->inject();
            foreach ($carrier as $key => $value) {
                $key      = 'x-' . str_replace('_', '-', $key);
                $header[] = sprintf('%s:%s', $key, $value);
            }

            if (!empty($header)) {
                $call->channel->updateOpts([
                    'header' => $header
                ]);
            }

            $sub_spans = [];
            if ($is_batch) {
                foreach ($call->calls as $rpc) {
                    $sub = GlobalTracer::createSpan($rpc->method, 'SUB_RPC', [
                        'parent_span' => $span
                    ]);

                    if (property_exists($rpc, 'params')) {
                        $sub->setTag('request_params', $rpc->params);
                    }
                    $sub->start();
                    array_push($sub_spans, $sub);
                }
            }

            $ctx->is_batch  = $is_batch;
            $ctx->span      = $span;
            $ctx->sub_spans = $sub_spans;
        });

        ls_on('client.afterCall', function ($event, $ctx, $call, $err, $result) {
            $is_batch  = $ctx->is_batch;
            $span      = $ctx->span;
            $sub_spans = $ctx->sub_spans;

            if ($is_batch) {
                foreach ($sub_spans as $k => $span) {
                    if ($err) {
                        $span->setError($err[$k]->code, $err[$k]->message);
                    }
                }
            } else {
                if ($err) {
                    $span->setError($err->code, $err->message);
                }
            }

            foreach ($sub_spans as $span) {
                $span->finish();
            }
            GlobalTracer::finishSpan($span);
        });

        ls_on('server.beforeDispatch', function ($event, $ctx, $call, $server) {
            $span = GlobalTracer::createSpan($call->method, 'DISPATCH');
            if (!empty($call->params)) {
                $span->setTag('request_params', $call->params);
            }
            GlobalTracer::startSpan($span);

            $ctx->span = $span;
        });

        ls_on('server.afterDispatch', function ($event, $ctx, $call, $err, $result, $server) {
            $span = $ctx->span;

            if ($err) {
                $span->setError($err->code, $err->message);
            }
            GlobalTracer::finishSpan($span);
        });
    }
}
