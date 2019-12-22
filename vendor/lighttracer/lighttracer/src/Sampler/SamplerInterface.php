<?php
/**
 * Sample 抽样的接口
 */

namespace LightTracer\Sampler;

interface SamplerInterface
{
    /**
     * 抽样
     * @return bool true代表抽中
     */
    public function shouldSample();
}
