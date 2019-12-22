<?php

namespace LightTracer\Writer;

use LightTracer\Util\Debug as DebugUtil;

class ZipkinWriter extends AbstractWriter
{
    private $api_url;

    public function __construct($api_url = 'http://127.0.0.1:9411/api/v1/spans')
    {
        $this->api_url = $api_url;
    }

    public function write($log)
    {
        $data = [
            'id'        => $log['id'],
            'traceId'   => $log['trace_id'],
            'name'      => $log['name'],
            'timestamp' => $log['timestamp'],
            'duration'  => $log['duration'],
            'debug'     => true,
        ];

        if ($log['parent_id']) {
            $data['parentId'] = $log['parent_id'];
        }

        $tags      = $log['tags'];
        $end_point = [];

        if ($tags['service_name']) {
            $end_point['serviceName'] = $tags['service_name'];
        }

        if ($tags['ipv4']) {
            $end_point['ipv4'] = $tags['ipv4'];
        }

        if (array_key_exists('port', $tags)) {
            $end_point['port'] = $tags['port'];
        }

        if (array_key_exists('errno', $tags) && $tags['errno'] && array_key_exists('errstr', $tags)) {
            $tags['error'] = $tags['errstr'];
            unset($tags['errstr']);
        }

        $tag_prefix = '';
        if (array_key_exists('side', $tags)) {
            $tag_prefix = $tags['side'] . '.';
        }

        $data['binaryAnnotations'] = [];
        foreach ($tags as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            array_push($data['binaryAnnotations'], [
                'key'      => ($key == 'error') ? $key : ($tag_prefix . $key),
                'value'    => is_array($value) ? json_encode($value) : ($value . ''),
                'endpoint' => $end_point
            ]);
        }

        $events              = $log['events'];
        $data['annotations'] = [];
        foreach ($events as $event) {
            if (is_null($event['value'])) {
                continue;
            }

            $event['endpoint'] = $end_point;
            $event['value']    = is_array($event['value']) ?
                json_encode($event['value']) : ($event['value'] . '');
            array_push($data['annotations'], $event);
        }

        $posts = [];
        array_push($posts, $data);

        $posts_string = json_encode($posts);
        if (!$posts_string) {
            $this->triggerError(json_last_error_msg());
            return false;
        }

        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $posts_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($posts_string)]);

        $error = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->triggerError('curl zipkin ' . $this->api_url . ' : ' . curl_error($ch));
            return false;
        }

        if ($error) {
            $this->triggerError($error);
            return false;
        }

        if (DebugUtil::isDebug()) {
            $detail_url = str_replace('api/v1/spans', 'traces/' . $log['trace_id'], $this->api_url);
            DebugUtil::log($detail_url);
        }

        return true;
    }
}
