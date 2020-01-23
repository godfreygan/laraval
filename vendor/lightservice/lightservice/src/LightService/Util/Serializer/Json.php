<?php
/**
 * json serializer for jsonrpc
 *
 * @author yuanbaoju
 */

namespace LightService\Util\Serializer;

class Json
{
    public function serialize($data)
    {
        $ret = json_encode($data);
        $last_error = json_last_error();

        if ($last_error) {
            throw new \UnexpectedValueException(sprintf(
                'failed to json_encode %s, %s',
                print_r($data, true),
                json_last_error_msg()
            ), $last_error);
        }

        return $ret;
    }

    public function deserialize($buf)
    {
        $ret = json_decode($buf, true);
        $last_error = json_last_error();

        if ($last_error) {
            throw new \Exception(
                sprintf('failed to json_decode %s, %s', $buf, json_last_error_msg()),
                $last_error
            );
        }

        return $ret;
    }
}
