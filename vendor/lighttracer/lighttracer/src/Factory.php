<?php

namespace LightTracer;

use LightTracer\Sampler\RandSampler;
use LightTracer\Trace\Tracer;
use LightTracer\Trace\EndPoint;
use LightTracer\Util\Debug as DebugUtil;
use LightTracer\Util\FlakeId;
use LightTracer\Writer\ConsoleWriter;
use LightTracer\Writer\FileWriter;

class Factory
{
    /**
     * php.ini
     *
     * endpoint_name=com.lighttracer
     * endpoint_ipv4=202.202.202.202
     * endpoint_port=80
     * endpoint_version=1.0.0
     * trace_sample_rate=1.0
     * trace_log_path=/tmp/trace_log
     * trace_log_span=86400
     *
     * @param array $conf 初始化参数
     *
     * @return Tracer
     */
    public static function createTracer($conf = [])
    {
        if (!array_key_exists('name', $conf)) {
            $conf['name'] = 'unknown';
        }

        if (!array_key_exists('endpoint', $conf)) {
            $domain = self::getConfByName('endpoint_name', $conf);
            $domain = $domain ? $domain : gethostname();

            $ipv4 = self::getConfByName('endpoint_ipv4', $conf);
            $ipv4 = $ipv4 ? $ipv4 : FlakeId::getIpv4();

            $port = self::getConfByName('endpoint_port', $conf);
            $port = $port ? $port : 0;

            $version = self::getConfByName('endpoint_version', $conf);
            $version = $version ? $version : 0;

            $conf['endpoint'] = new EndPoint($domain, $version, $ipv4, $port, getmypid());
        }

        if (!array_key_exists('trace_sample_rate', $conf)) {
            $conf['trace_sample_rate'] = 1.0;
        }

        if (!array_key_exists('writers', $conf)) {
            if (self::getConfByName('trace_log_path', $conf) !== null) {
                $conf['writers'] = [new FileWriter(
                    'trace_log',
                    self::getConfByName('trace_log_path', $conf),
                    self::getConfByName('trace_log_span', $conf),
                    'log'
                )];
            } else {
                $conf['writers'] = [new ConsoleWriter()];
            }
        }

        if (!array_key_exists('sampled', $conf)) {
            if (self::getConfByName('trace_sample_rate', $conf) !== null) {
                $sampler         = new RandSampler(self::getConfByName('trace_sample_rate', $conf));
                $conf['sampled'] = $sampler->shouldSample();
            } else {
                $conf['sampled'] = true;
            }
        }

        $tracer = new Tracer($conf);

        if (!(array_key_exists('auto_start', $conf) && !$conf['auto_start'])) {
            DebugUtil::log('auto_start');
            $tracer->start();
        }

        if (!(array_key_exists('auto_finish', $conf) && !$conf['auto_finish'])) {
            register_shutdown_function(function ($tracer) {
                DebugUtil::log('auto_finish');
                $tracer->finish();
            }, $tracer);
        }

        if (array_key_exists('tags', $conf) && is_array($conf['tags'])) {
            foreach ($conf['tags'] as $k => $v) {
                $tracer->setTag($k, $v);
            }
        }

        return $tracer;
    }

    protected static function getConfByName($name, $conf = [])
    {
        if (array_key_exists($name, $conf)) {
            return $conf[$name];
        }

        if (array_key_exists($name, $_ENV)) {
            return $_ENV[$name];
        }

        if (ini_get($name) !== false) {
            return ini_get($name);
        }

        return '';
    }
}
