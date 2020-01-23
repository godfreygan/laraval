<?php
/**
 * base class for storing error infomation
 *
 * @author yuanbaoju
 */

namespace LightService\Util;

class Errorable
{
    private $errstr_ = '';
    private $errno_  = 0;

    public function errno()
    {
        return $this->errno_;
    }

    public function errstr()
    {
        return $this->errstr_;
    }

    protected function clearErr()
    {
        $this->errstr_ = '';
        $this->errno_  = 0;
    }

    protected function setErr($no, $str)
    {
        $this->errstr_ = $str;
        $this->errno_  = $no;
    }
}
