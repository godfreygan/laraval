<?php

namespace App\Blog\Controllers;

use App\Blog\Library\Enum\ExceptionCodeEnum;
use App\Blog\Library\Exceptions\ServiceException;
use CjsLsf\Core\App;

class BaseController extends Base
{

    private $view_base_path = '';   // views根路径
    private $view = '';             // view路径
    private $data = array();        // 页面传递数据

    public function __construct()
    {
        parent::__construct();
        $this->view_base_path = App::appPath() . '/' . getModuleName() . '/Views/';
    }

    /**
     * @title: 创建视图
     * @param $viewName
     * @return View
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public function display($viewName)
    {
        if (! $viewName) {
            throw new ServiceException("视图名称不能为空！");
        } else {

            $viewFilePath = $this->view_base_path . $viewName;
            if (is_file($viewFilePath)) {
                $this->view = $viewFilePath;
            } else {
                throw new ServiceException("视图文件不存在！");
            }
        }
    }

    /**
     * @title: 视图变量传递
     * @param $key
     * @param $value
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public function assign($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
    }

    /**
     * 传输视图及变量
     */
    public function __destruct()
    {
        if (! empty($this->data)) extract($this->data);
        if(! empty($this->view)) require $this->view;

    }

}