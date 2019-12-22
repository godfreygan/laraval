<?php

namespace LightTracer\Writer;

use LightTracer\Util\Debug as DebugUtil;

class KafkaWriter extends AbstractWriter
{
    private $api_url;
    private $username;
    private $password;

    public function __construct(
        $api_url = 'http://10.59.74.139:5000/post/trace',
        $username = 'admin',
        $password = 'admin'
    )
    {
        $this->api_url  = $api_url;
        $this->username = $username;
        $this->password = $password;
    }

    public function write($log)
    {
        $posts_string = urlencode(json_encode($log, false));
        $posts_string = "message={$posts_string}&batch=false";

        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $posts_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->triggerError('curl kafka ' . $this->api_url . ' : ' . curl_error($ch));
            return false;
        }

        $result = json_decode($response, true);
        if ($result['status']) {
            $this->triggerError($result['message']);
            return false;
        }

        DebugUtil::log($log['trace_id']);
        DebugUtil::log($response);

        return true;
    }
}
