<?php
/**
 * 验证码模块
 *
 * @author Yuan B.J.
 * @copyright Yuan B.J., 2014.09.17
 */
class Captcha
{

    /**
     * 生成验证码
     *
     * @return string 验证码
     * @author Yuan B.J.
     */
    public function generate()
    {
        return mt_rand(1000, 9999);
    }

    /**
     * 废弃的方法
     *
     * @deprecated
     * @return void
     * @author Yuan B.J.
     */
    public function deprecatedMethod()
    {
        return NULL;
    }

    /**
     * 私有的方法
     *
     * @return void
     * @author Yuan B.J.
     */
    private function privateMethod()
    {
        return NULL;
    }
}
