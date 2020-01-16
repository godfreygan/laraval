<?php

namespace App\Blog\Controllers;

use App\Util\ValidatorUtil;
use App\Blog\Modules\Queue\Kafka\KafkaResend;
use CjsRedis\Redis;
use App\Blog\Library\Exceptions\ServiceException;
use App\Blog\Library\Enum\ExceptionCodeEnum;

class IndexController extends Base
{

    public function indexAction()
    {
        return $this->responseSuccess(['tips' => 'web服务正常'], __METHOD__);
    }

    /**
     *
     * @OA\Post(
     *      tags={"other"},
     *      path="/rpc.php?index_clearnredis",
     *      summary="删除redis",
     *      operationId="index_clearnredis",
     *      description="",
     *
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *                  mediaType="application/json",
     *           @OA\Schema(
     *                  allOf={
     *                     @OA\Schema(ref="#/components/schemas/request_rpc_common_param"),
     *                     @OA\Schema(@OA\Property(property="params",type="array",@OA\Items(ref="#/components/schemas/index_clearnredis"))),
     *                     @OA\Schema(@OA\Property(property="method",type="string",example="index.clearnRedis")),
     *                 }
     *           )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *         @OA\JsonContent(
     *           allOf={
     *               @OA\Schema(ref="#/components/schemas/apiprotocol"),
     *            },
     *        ),
     *    ),
     * )
     */
    public function clearnRedisAction($param = []){
        $rulesMap = [
            'group' => ['required|string','RedisGroup'],
            'key' => ['required|string','RedisKey'],
        ];
        try{
            list($rules,$message) = ValidatorUtil::formatRule($rulesMap);
            $requestData = $this->validate($param,$rules,$message,TRUE, __METHOD__);
            $redisGroup = $requestData['group'];
            $redisKeyList = explode(',',$requestData['key']);
            foreach ($redisKeyList as $redisKey) {
                if(Redis::exists($redisGroup,$redisKey)){
                    continue;
                }
                Redis::del($redisGroup,$redisKey);
            }
            return $this->responseSuccess('操作成功', __METHOD__);
        } catch (ServiceException $e) {
            $this->log(__METHOD__, $e->getCode(), $e->getMessage());
            return $this->responseError($e->getCode(), $e->getMessage(), null, __METHOD__);
        }


    }

    /**
     *
     * @OA\Post(
     *      tags={"other"},
     *      path="/rpc.php?index_sendkafkamessage",
     *      summary="发送kafka消息",
     *      operationId="index_sendkafkamessage",
     *      description="",
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *                  mediaType="application/json",
     *           @OA\Schema(
     *                  allOf={
     *                     @OA\Schema(ref="#/components/schemas/request_rpc_common_param"),
     *                     @OA\Schema(@OA\Property(property="params",type="array",@OA\Items(ref="#/components/schemas/index_sendkafkamessagerequest"))),
     *                     @OA\Schema(@OA\Property(property="method",type="string",example="index.sendKafkaMessage")),
     *                 }
     *           )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *         @OA\JsonContent(
     *           allOf={
     *               @OA\Schema(ref="#/components/schemas/apiprotocol"),
     *            },
     *        ),
     *    ),
     * )
     */
    public function sendKafkaMessageAction($params = [])
    {
        $rulesMap = [
            'queue' => ['required|string', 'queue'],
            'type'  => ['required|string', 'type'],
            'data'  => ['required|array', 'data'],
            'id'    => ['sometimes|string', 'id'],
        ];
        try{
            list($rules, $message) = ValidatorUtil::formatRule($rulesMap);
            $data = $this->validate($params,$rules,$message,TRUE, __METHOD__);
            $id = empty($data['id']) ? NULL : $data['id'];
            $ret = (new KafkaResend())->send02($data['queue'], $data['type'], $data['data'], $id);
            if($ret){
                return $this->responseSuccess('操作成功', __METHOD__);
            }else{
                return $this->responseError(ExceptionCodeEnum::HANDEL_FAIL, '操作失败', null, __METHOD__);
            }
        } catch (ServiceException $e) {
            $this->log(__METHOD__, $e->getCode(), $e->getMessage());
            return $this->responseError($e->getCode(), $e->getMessage(), null, __METHOD__);
        }
    }
}