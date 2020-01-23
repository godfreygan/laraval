<?php
/**
 * msgpack serializer for jsonrpc
 *
 * @author yuanbaoju
 */

namespace LightService\Util\Serializer;

class Msgpack
{
    public function serialize($data)
    {
        return msgpack_pack($data);
    }

    public function deserialize($buf)
    {
        return msgpack_unpack($buf);
    }
}
