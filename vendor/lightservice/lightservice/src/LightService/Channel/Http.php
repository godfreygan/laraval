<?php
/**
 * local implementation of LightService
 *
 * @author yuanbaoju
 */

namespace LightService\Channel;

use LightService\Exception\CurlException;

class Http extends Channel
{
    private $header = [];
    private $query = [];
    private $target = null;
    private $ch = null;

    public function __construct($target, $opts = [])
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            // for request error info, e.g. 404, 403, 500 ...
            CURLOPT_FAILONERROR => 1,
            CURLOPT_NOSIGNAL => 1,
            CURLOPT_TIMEOUT_MS => 500
        ]);

        $this->ch = $ch;
        $this->target = $target;
        $this->updateOpts($opts);
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function updateOpts($opts)
    {
        $array = [];

        foreach ($opts as $k => $v) {
            switch ($k) {
                case 'exec_timeout':
                    $array[CURLOPT_TIMEOUT_MS] =  $v;
                    break;
                case 'dns_timeout':
                    $array[CURLOPT_DNS_CACHE_TIMEOUT] = $v;
                    break;
                case 'connect_timeout':
                    $array[CURLOPT_CONNECTTIMEOUT_MS] = $v;
                    break;
                case 'header':
                    $this->header = array_merge($this->header, $v);
                    break;
                case 'query':
                    $this->query = array_merge($this->query, $v);
                    break;
                case 'ssl_verifypeer':
                    $array[CURLOPT_SSL_VERIFYPEER] = $v;
                    break;
                case 'ssl_verifyhost':
                    $array[CURLOPT_SSL_VERIFYHOST] = $v;
                    break;
            }
        }

        return curl_setopt_array($this->ch, $array);
    }

    public function send($message, $opts = [])
    {
        $url = $this->target;

        if (array_key_exists('path', $opts)) {
            $url .= '/' . $opts['path'];
        }

        $query = $this->query;

        if (array_key_exists('query', $opts)) {
            $query = array_merge($this->query, $opts['query']);
        }

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $curl_opts = [
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $message
        ];

        $header = $this->header;

        if (array_key_exists('header', $opts)) {
            $header = $opts['header'] + $header;
        }

        if (!empty($header)) {
            $curl_opts[CURLOPT_HTTPHEADER] = $header;
        }

        curl_setopt_array($this->ch, $curl_opts);
        return $this;
    }

    public function wait()
    {
        $reply = curl_exec($this->ch);

        if (false === $reply) {
            throw new CurlException(curl_error($this->ch), curl_errno($this->ch));
        }

        return $reply;
    }

    public static function waitAll($channels)
    {
        $mh = curl_multi_init();

        if (!$mh) {
            throw new CurlException('failed to curl_multi_init');
        }

        foreach ($channels as $channel) {
            curl_multi_add_handle($mh, $channel->ch);
        }

        do {
            $rc = curl_multi_exec($mh, $still_running);
        } while (CURLM_CALL_MULTI_PERFORM === $rc);

        $repeats = 0;

        while ($still_running && CURLM_OK === $rc) {
            $n = curl_multi_select($mh, 1000);

            if ($n <= 0) {
                ++$repeats;

                if ($repeats > 1) {
                    usleep(250);
                }
            } else {
                $repeats = 0;
            }

            do {
                $rc = curl_multi_exec($mh, $still_running);
            } while (CURLM_CALL_MULTI_PERFORM === $rc);
        }

        $ret = false;

        if (CURLM_OK === $rc) {
            $ret = [];
            $storage = [];
            $msgs_in_queue = 0;

            do {
                $result = curl_multi_info_read($mh, $msgs_in_queue);
                $storage[(int)$result['handle']] = $result['result'];
            } while ($msgs_in_queue);

            foreach ($channels as $channel) {
                $errno = $storage[(int)$channel->ch];

                if ($errno) {
                    $ret[] = new CurlException(curl_strerror($errno), $errno);
                } else {
                    $ret[] = curl_multi_getcontent($channel->ch);
                }
            }

            curl_multi_close($mh);
            return $ret;
        }

        $err = null;

        switch ($rc) {
            case CURLM_BAD_HANDLE:
                $err = 'curlm bad handle';
                break;
            case CURLM_BAD_EASY_HANDLE:
                $err = 'curlm bad easy handle';
                break;
            case CURLM_OUT_OF_MEMORY:
                $err = 'curlm out of memory';
                break;
            case CURLM_INTERNAL_ERROR:
                $err = 'curlm internal error';
                break;
            default:
                $err = 'curlm unknown internal error';
        }

        curl_multi_close($mh);
        throw new CurlException($err, $rc);
    }

    public function __destruct()
    {
        curl_close($this->ch);
    }
}
