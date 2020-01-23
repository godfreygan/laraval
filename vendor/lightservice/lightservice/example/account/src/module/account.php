<?php
/**
 * 用户模块
 *
 * @author Yuan B.J.
 * @copyright Yuan B.J., 2014.09.17
 */
class Account
{
    private $account_ = array(
        'ali' => 'baba',
        'foo' => 'bar'
    );

    /**
     * 登陆
     *
     * @param string $name 用户名
     * @param string $passwd 密码
     * @return boolean true 成功 false 失败
     * @author Yuan B.J.
     */
    public function login($name, $passwd)
    {
        if (!isset($this->account_[$name])) {
            return false;
        }

        if (md5($this->account_[$name]) !== md5($passwd)) {
            return false;
        }

        return true;
    }

    /**
     * 欢迎
     *
     * @param string $name 用户名
     * @return string
     * @author Yuan B.J.
     */
    public function welcome($name)
    {
        return 'hello ' . $name;
    }
}
