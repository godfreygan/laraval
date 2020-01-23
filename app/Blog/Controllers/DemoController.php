<?php

namespace App\Blog\Controllers;

use App\Blog\Library\Exceptions\ServiceException;
use App\Blog\Library\Util\DBUtil;
use App\Blog\Modules\Queue\Kafka\KafkaResend;
use CjsRedis\Sequence;
use App\Util\ValidatorUtil;

/**
 * Class DemoController
 * @package App\Blog\Controllers
 * @remark  此文件仅可用于开发环境、测试环境。禁止生产环境调用。
 */
class DemoController extends Base
{
    public function __construct()
    {
        parent::__construct();
        parent::denyProductionExec();   //  禁止生产环境执行
    }

    public function getSeqAction()
    {
        $user_id = '0005000391';
        //使用UserId获取分库分表规则
        var_export(DBUtil::getInstance()->getDBBaseByStr($user_id));
        exit;
        //根据用户ID生成序列号
        $ono = Sequence::getNextGlobalId('t_user', $user_id);
        echo "<br>";
        echo "ByUserID: " . $ono . PHP_EOL;
        echo "<br>";
        //使用序列号获取分库分表规则
        var_export(DBUtil::getInstance()->getDBBaseBySeq($ono));
        echo "<br>";


        //根据序列号生成序列号
        $seq = Sequence::getNextGlobalSeq('t_user', $ono);
        echo "ByOno: " . $seq . PHP_EOL;
        echo "<br>";
        //使用序列号获取分库分表规则
        var_export(DBUtil::getInstance()->getDBBaseBySeq($seq));
        echo "<br>";

    }

    public function getImgUrlAction()
    {
        $key    = 'Fnkoc0yQaH8-mlKq52LeAv4Ty3Vk';
        $bucket = 'prd_pub';
        $result = \App\Util\Img::instance()->getImg($key, $bucket, 500, 500);
        var_dump($result);
        die;
    }

    /**
     * @title: 重新发送kafka消息
     * @param array $params
     * @throws ServiceException
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public function setKafkaMessageAction($params = [])
    {
        $rulesMap = [
            'queue' => ['required|string', 'queue'],
            'type'  => ['required|string', 'type'],
            'data'  => ['required|array', 'data'],
            'id'    => ['sometimes|string', 'id'],
        ];
        list($rules, $message) = ValidatorUtil::formatRule($rulesMap);
        $data = $this->validate($params, $rules, $message, TRUE, __METHOD__);
        $id   = empty($data['id']) ? NULL : $data['id'];
        var_dump((new KafkaResend())->send02($data['queue'], $data['type'], $data['data'], $id));
        die;
    }
}
