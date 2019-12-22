<?php
/**
 * EndPoint 标识是自己是谁，在OpenTracing称为Peer
 */

namespace LightTracer\Trace;

class EndPoint
{
    private $version = null;
    private $service_name = null;
    private $ipv4 = null;
    private $port = null;
    private $pid = null;

    /**
     * 初始化EndPoint
     * @param $service_name 服务名，可以是域名
     * @param $version 服务的版本号
     * @param $ipv4 当前服务器的IP
     * @param $port 端口
     * @param $pid
     */
    public function __construct($service_name = null, $version = null, $ipv4 = null, $port = null, $pid = null)
    {
        $this->service_name = $service_name;
        $this->version      = $version;
        $this->ipv4         = $ipv4;
        $this->port         = $port;
        $this->pid          = $pid;
    }

    public function getServiceName()
    {
        return $this->service_name;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getIpv4()
    {
        return $this->ipv4;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPid()
    {
        return $this->pid;
    }
}
