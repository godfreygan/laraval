<?php
/**
 * RandSample 随机抽样
 */

namespace LightTracer\Sampler;

class RandSampler implements SamplerInterface
{
    public $rate = 1.0;

    /**
     * @param float $rate 抽样率 0.0-1.0，默认1.0，全部抽中
     */
    public function __construct($rate = 1.0)
    {
        $this->rate = $rate;
    }

    /**
     * 抽样
     * @return bool true代表抽中
     */
    public function shouldSample()
    {
        $hit = mt_getrandmax() * $this->rate;
        return (rand(1, mt_getrandmax()) <= $hit);
    }
}
