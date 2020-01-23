<?php
/**
 * mongodb objectid utilities
 *
 * @author yuanbaoju
 */

namespace LightService\Util;

class ObjectId
{
    private $id;
    private static $mathine_id;
    private static $pid_part;

    /**
     * constructor of ObjectId
     *
     * @param string $id id value which overrides internal value
     */
    public function __construct($id = null)
    {
        if (isset($id)) {
            $this->id = $id;
        } else {
            $this->id = self::generate();
        }
    }

    public static function getMachineId()
    {
        if (!isset(self::$mathine_id)) {
            self::$mathine_id = substr(md5(getHostName()), 0, 6);
        }

        return self::$mathine_id;
    }

    private static function getPidPart()
    {
        if (!isset(self::$pid_part)) {
            self::$pid_part = getmypid() & 0xFFFF;
        }

        return self::$pid_part;
    }

    private static function nextCounter()
    {
        return mt_rand(0, 0xFFFFFF);
    }

    /**
     * generate objectid in hex string
     *
     * @return string 24 bytes unique id
     */
    public static function generate()
    {
        return unpack(
            'H24',
            pack('NH3nN', time() & 0xFFFFFFFF, self::getMachineId(), self::getPidPart(), self::nextCounter())
        )[1];
    }

    public function __toString()
    {
        return $this->id;
    }
}
