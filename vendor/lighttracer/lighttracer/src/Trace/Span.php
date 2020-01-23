<?php
/**
 * Span 追踪的基本单位，一个时间段，可以是一次RPC，一次HTTP请求等
 * 这个类是追踪和统计系统的核心类
 */

namespace LightTracer\Trace;

use LightTracer\Util\FlakeId as FlakeId;
use LightTracer\Util\Time as TimeUtil;

class Span
{
    /**
     * 为了限制一条日志的大小，记录的字符数有限制
     * name <= 64 char
     * type <= 64 char
     * events.value <= 64 char
     * tags.key <= 64 char
     * tags.value <= 512 char
     * count(tags) <= 50
     * count(events) <= 50
     */

    // 版本号
    const VERSION = '1.1.0';

    // CS模型中的Client 和 Server
    const SPAN_SIDE_SERVER = 'server';
    const SPAN_SIDE_CLIENT = 'client';

    // Span的数据结构
    private $trace_id = null;
    private $id = null;
    private $parent_id = null;
    private $side = null;
    private $writer = null;
    private $endpoint = null;
    private $timestamp = 0;
    private $duration = 0;
    private $tags = ['tracer_version' => self::VERSION];
    private $events = [];
    private $baggage = [];
    private $operation_name;
    private $operation_type;

    /**
     * Span constructor.
     * @param string $operation_name 方法名
     * @param string $operation_type 类型
     */
    public function __construct($operation_name = '', $operation_type = '')
    {
        $this->operation_name = $this->safeString($operation_name);
        $this->operation_type = $this->safeString($operation_type);
        $this->id             = FlakeId::generate64();
        $this->tags['dir']    = preg_replace('/(\/vendor\/lighttracer)?\/src\/Trace\/Span.php/', '', __FILE__);
    }

    /**
     * Span 开始计时，如果Span是CS模型，会产生cs/sr事件
     *
     * @param $us
     *
     * @return $this
     */
    public function start($us = null)
    {
        if ($this->side === self::SPAN_SIDE_CLIENT) {
            $this->logEvent('cs');
        } elseif ($this->side === self::SPAN_SIDE_SERVER) {
            $this->logEvent('sr');
        }

        $this->timestamp = self::microNow($us);
        return $this;
    }

    public function scope($func, $options = [])
    {
        $this->start();

        try {
            return $func($this);
        } catch (\Exception $e) {
            if (!array_key_exists('ignore_exception', $options) ||
                !$options['ignore_exception']) {
                $this->setError(555, $e->getMessage());
            }

            throw $e;
        } finally {
            $this->finish();
        }
    }

    /**
     * Span 结束计时，如果Span是CS模型，会产生cr/ss事件
     * 当设置了writer，finish时候会自动保存
     * @param $us
     * @return bool 是否保存成功
     */
    public function finish($us = null)
    {
        if ($this->side === self::SPAN_SIDE_CLIENT) {
            $this->logEvent('cr');
        } elseif ($this->side === self::SPAN_SIDE_SERVER) {
            $this->logEvent('ss');
        }

        $this->duration = self::microNow($us) - $this->timestamp;
        return $this->save();
    }

    /**
     * 保存当前Span至writer中
     * @return bool 是否保存成功
     */
    public function save()
    {
        if (empty($this->writer)) {
            return false;
        }

        $log = [
            'id'        => $this->id,
            'parent_id' => $this->parent_id,
            'trace_id'  => $this->trace_id,
            'name'      => $this->operation_name,
            'timestamp' => $this->timestamp,
            'duration'  => $this->duration,
        ];

        $log['tags']         = $this->tags;
        $log['events']       = $this->events;
        $log['tags']['type'] = $this->operation_type;

        if ($this->side) {
            $log['tags']['side'] = $this->side;
        }

        // 将EndPoint保存到 Tags中
        $endpoint = $this->endpoint();
        if ($endpoint) {
            if ($endpoint->getVersion()) {
                $log['tags']['version'] = $endpoint->getVersion();
            }

            if ($endpoint->getPort()) {
                $log['tags']['port'] = $endpoint->getPort();
            }

            if ($endpoint->getServiceName()) {
                $log['tags']['service_name'] = $endpoint->getServiceName();
            }

            if ($endpoint->getIpv4()) {
                $log['tags']['ipv4'] = $endpoint->getIpv4();
            }

            if ($endpoint->getPid()) {
                $log['tags']['pid'] = $endpoint->getPid();
            }
        }

        // 将数据写入Writer中
        if (is_array($this->writer)) {
            $result = true;
            foreach ($this->writer as $w) {
                if (!$w->write($log)) {
                    $result = false;
                }
            }
            return $result;
        } else {
            return $this->writer->write($log);
        }
    }

    /**
     * 打包当前Span，用于传输给下游
     * @param array $baggage 键值对
     * @return array
     */
    public function inject($baggage = [])
    {
        $carrier = [
            'trace_id'       => $this->trace_id,
            'span_id'        => $this->id,
            'parent_id'      => $this->parent_id,
            'operation_name' => $this->operation_name,
            'operation_type' => $this->operation_type
        ];

        $carrier = array_merge($carrier, $baggage);
        return $carrier;
    }

    /** 从inject中解出Span数据
     * @param array $carrier
     */
    public function extract(array $carrier)
    {
        if (array_key_exists('trace_id', $carrier)) {
            $this->trace_id = $carrier['trace_id'];
            unset($carrier['trace_id']);
        }
        if (array_key_exists('span_id', $carrier)) {
            $this->id = $carrier['span_id'];
            unset($carrier['span_id']);
        }
        if (array_key_exists('parent_id', $carrier)) {
            $this->parent_id = $carrier['parent_id'];
            unset($carrier['parent_id']);
        }
        if (array_key_exists('operation_name', $carrier)) {
            $this->operation_name = $carrier['operation_name'];
            unset($carrier['operation_name']);
        }
        if (array_key_exists('operation_type', $carrier)) {
            $this->operation_type = $carrier['operation_type'];
            unset($carrier['operation_type']);
        }

        $this->baggage = $carrier;
    }

    private function microNow($us = null)
    {
        return (!is_null($us) && filter_var($us, FILTER_VALIDATE_INT)) ? $us : TimeUtil::microNow();
    }

    /**
     * 记录事件
     * @param array $event 事件名
     * @param string $us
     * @return bool
     */
    public function logEvent($event, $us = null)
    {
        if (count($this->events) > 50) {
            return false;
        }

        array_push($this->events, [
            'timestamp' => self::microNow($us),
            'value'     => self::safeString($event)
        ]);

        return true;
    }

    /**
     * 设置标签
     * @param string $key 标签名
     * @param string $value 标签值，标量或者数组
     * @param int $max_len
     * @return bool
     */
    public function setTag($key, $value, $max_len = 512)
    {
        if (count($this->tags) > 50 && !array_key_exists(self::safeString($key), $this->tags)) {
            return false;
        }

        $this->tags[self::safeString($key)] = self::safeString($value, $max_len);
        return true;
    }

    /**
     * 获取所有标签
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    public function getTag($key)
    {
        if (array_key_exists($key, $this->tags)) {
            return $this->tags[$key];
        }

        return null;
    }

    /**
     * 设置错误
     */
    public function setError($errno = 1, $errstr = 'unknown error')
    {
        $this->setTag('errno', $errno);
        $this->setTag('errstr', $errstr);
        $this->logEvent('error');
    }

    public function getBaggage()
    {
        return $this->baggage;
    }

    /**
     * 设置parentSpan，从中读取 $span_id, $trace_id, $writer
     * @param Span $span
     */
    public function setParentSpan(Span $span)
    {
        $this->parent_id = $span->spanId();
        $this->trace_id  = $span->traceId();
        $this->writer    = $span->writer();
        $this->endpoint  = $span->endpoint();
    }

    /**
     * CS模型，设置是client还是server
     * client:  start 和 finish 时候生成 cs cr 日志
     * server:  start 和 finish 时候生成 sr ss 日志
     * @param bool $side
     * @return bool
     */
    public function side($side = null)
    {
        if ($side) {
            $this->side = $side;
        }

        return $this->side;
    }

    public function setSideToServer()
    {
        $this->side(self::SPAN_SIDE_SERVER);
    }

    public function setSideToClient()
    {
        $this->side(self::SPAN_SIDE_CLIENT);
    }

    public function serverSide()
    {
        return self::SPAN_SIDE_SERVER;
    }

    public function clientSide()
    {
        return self::SPAN_SIDE_CLIENT;
    }

    public function spanId($span_id = null)
    {
        if ($span_id) {
            $this->id = $span_id;
        }

        return $this->id;
    }

    public function traceId($trace_id = null)
    {
        if ($trace_id) {
            $this->trace_id = $trace_id;
        }

        return $this->trace_id;
    }

    public function writer($writer = null)
    {
        if ($writer) {
            $this->writer = $writer;
        }

        return $this->writer;
    }

    public function endpoint($endpoint = null)
    {
        if ($endpoint) {
            $this->endpoint = $endpoint;
        }

        return $this->endpoint;
    }

    public static function version()
    {
        return self::VERSION;
    }

    private static function safeString($value, $max_len = 64)
    {
        if (is_string($value)) {
            $result = self::escapeInvalidChars($value);
            return self::shortString($result, $max_len);
        } elseif (is_null($value)) {
            return 'null';
        } elseif (is_scalar($value)) {
            return $value;
        } elseif (!is_array($value)) {
            return gettype($value);
        }

        $result = json_encode($value);

        if (!$result && json_last_error()) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $value[$k] = self::safeString($v);
                }

                $result = json_encode($value);
            } else {
                $result = 'value can not json_encode: ' . json_last_error_msg();
            }
        }

        return self::shortString($result, $max_len);
    }

    private static function escapeInvalidChars($string)
    {
        $patterns     = array('/</', '/>/');
        $replacements = array('&lt;', '&gt;');
        $string       = preg_replace($patterns, $replacements, $string);

        return html_entity_decode(htmlentities($string, ENT_IGNORE, 'UTF-8'));
    }

    private static function shortString($str, $max_len = 64)
    {
        if (mb_strlen($str) > $max_len) {
            return mb_substr($str, 0, $max_len - 4) . ' ...';
        }

        return $str;
    }
}
