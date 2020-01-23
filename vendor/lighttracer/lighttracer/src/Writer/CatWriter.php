<?php

namespace LightTracer\Writer;

use LightTracer\Util\FlakeId as FlakeId;
use LightTracer\Util\Debug as DebugUtil;
use LightTracer\Util\Time as TimeUtil;

class CatWriter extends AbstractWriter
{
    private $host;
    private $port;
    private $address;

    /**
     * @param $host string 服务器的地址
     * @param $port int 端口
     */
    public function __construct($host = '127.0.0.1', $port = 2280)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function write($log)
    {
        $id = self::catId($log);

        /**
         * {VERSION\tDomain\tHostName\tIP\tGroupName\tClusterId\tClusterName\t
         *  MessageId\tParentMessageId\tRootMessageId\tSessionToken\t\n}
         */
        $msg = self::catLine(['PT1', $log['tags']['service_name'], "", $log['tags']['ipv4'], '', '', '',
            $id, $id, $id, '']);

        /**
         * Format the transaction for sending. A transaction may be one
         * of the following formats.
         * 1. {ATimestamp\tType\tName\tStatus\tDuration\tData\t\n}
         * 2. {tTimestamp\tType\tName\t\n}
         * 3. {TTimestamp\tType\tName\tStatus\tDuration\tData\t\n}
         *
         * Format the event for sending. An event must obey the
         * following format.
         * {ETimestamp\tType\tName\tStatus\tDuration\tData\t\n}
         *
         */
        $status = "0";
        if (array_key_exists('errno', $log['tags']) && $log['tags']['errno']) {
            $status = $log['tags']['errno'];
        }

        // convert start and finish time  to cat format
        $start_time  = TimeUtil::catTime($log['timestamp']);
        $finish_time = TimeUtil::catTime($log['timestamp'] + $log['duration']);

        // transaction start
        $msg .= self::catLine(["t{$start_time}", $log['tags']['type'], $log['name']]);

        // events
        foreach ($log['events'] as $event) {
            $event_time = TimeUtil::catTime($event['timestamp']);
            $msg        .= self::catLine(["E{$event_time}", $log['tags']['type'], $event['value'], '0', '', '']);
        }

        // transaction finish
        $log['tags']['trace_id'] = $log['trace_id'];
        $msg                     .= self::catLine(["T{$finish_time}", $log['tags']['type'], $log['name'],
            $status, $log['duration'] . 'ms', json_encode($log['tags'])]);
        DebugUtil::log("cat_msg = $msg");

        /**
         * example:
         * t10:06:09.615 URL /cat/r/home
         * E10:06:09.615 URL URL.Server    IPS=10.59.85.12&VirtualIP=10.59.85.12&Server=10.59.85.12
         * E10:06:09.616 URL URL.Method    HTTP/GET /cat/r
         * A10:06:09.665 MVC InboundPhase    0.36ms
         * A10:06:09.666 MVC TransitionPhase   0.04ms
         * t10:06:09.668 MVC OutboundPhase
         * t10:06:09.689 URL.Forward /cat/r/home
         * E10:06:09.689 URL.Forward URL.Forward.Method    HTTP/GET /cat/jsp/report/home/home.jsp
         * T10:06:10.829 URL.Forward /cat/r/home   1140ms
         * T10:06:10.830 MVC OutboundPhase   1162ms
         * T10:06:10.830 URL /cat/r/home   1215ms module=r&in=home&out=home
         */
        return $this->sendMessageToCat($msg);
    }

    /**
     * 生成 CAT的消息格式
     * @param $line
     * @return string
     */
    private static function catLine($line)
    {
        return implode("\t", $line) . "\t\n";
    }

    /**
     * 生成 CAT的ID格式
     * @return string
     */
    private static function catId($log)
    {
        $hour_span = round($log['timestamp'] / 1000 / 1000 / 3600);
        $seq       = FlakeId::nextSeq($hour_span);

        if (array_key_exists('pid', $log['tags'])) {
            $pid = $log['tags']['pid'];
        } else {
            $pid = getmypid();
        }

        if ($seq < 0xffff) {
            // 2个字节pid  2个字节 seq (支持 65,536个序号)
            $index = (($pid & 0xffff) << 16) | $seq;
        } else {
            // 1个字节pid  3个字节 seq (支持 16,777,216个序号)
            $index = (($pid & 0xff) << 24) | ($seq & 0xffffff);
        }

        $id = implode('-', [
            $log['tags']['service_name'],
            FlakeId::getIpHex($log['tags']['ipv4']),
            $hour_span,
            $index]);

        DebugUtil::log("cat_id = $id");
        return $id;
    }

    /**
     * 向CAT服务器发送消息
     * @param $msg 消息内容
     * @return bool 是否发送成功
     */
    private function sendMessageToCat($msg)
    {
        $sock   = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $result = socket_connect($sock, $this->host, $this->port);
        if (!$result) {
            return false;
        }

        $data = pack('N', strlen($msg)) . $msg;
        socket_write($sock, $data, strlen($data));
        socket_close($sock);
        return true;
    }

    /**
     * 从CAT路由器取到API的地址
     */
    public function fetchAddressFromRoute($route_url = 'http://127.0.0.1:2281/cat/s/router?op=json&domain=cat')
    {
        $content       = file_get_contents($route_url);
        $json          = json_decode($content, true);
        $serversConfig = $json['kvs']['routers'];
        preg_split('/;/', $serversConfig);
        list($host, $port) = preg_split('/:/', preg_split('/;/', $serversConfig)[0]);
        $this->address = $host;
        $this->port    = $port;
    }
}
