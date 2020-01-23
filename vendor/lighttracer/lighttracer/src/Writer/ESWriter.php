<?php

namespace LightTracer\Writer;

use LightTracer\Util\Time as TimeUtil;

class ESWriter extends AbstractWriter
{
    private $api_url;

    public function __construct($api_url = 'http://127.0.0.1:9200')
    {
        $this->api_url = $api_url;
    }

    public function write($log)
    {
        $index = $log['tags']['service_name'] . '-' . self::esDate($log['timestamp']);
        $type  = 'span';

        $log['time'] = TimeUtil::catTime($log['timestamp']);
        foreach ($log['events'] as &$event) {
            $event['time'] = TimeUtil::catTime($event['timestamp']);
        }

        $url = $this->api_url . "/$index/$type";

        $posts_string = json_encode($log, false);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $posts_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($posts_string)]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->triggerError('curl es ' . $url . ' : ' . curl_error($ch));
            return false;
        }

        $result = json_decode($response, true);
        if (isset($result['error'])) {
            $this->triggerError(json_encode($result['error']));
            return false;
        }

        return true;
    }

    private static function esDate($ms)
    {
        $time = floor($ms / 1000.0 / 1000.0);
        return date("Y.m.d", $time);
    }
}
