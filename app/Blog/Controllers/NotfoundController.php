<?php

namespace App\Blog\Controllers;
/**
 * 404页面
 *
 */

use Log;

class NotfoundController extends Base
{

    /**
     * 404 地址
     * todo待完善
     * @OA\Get(
     *     path="/rpc.php?notfound_index",
     *     tags={"other"},
     *     summary="404接口地址返回",
     *     operationId = "notfound_index",
     *     description="
    找不到的接口地址统一返回 ",
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *         @OA\JsonContent(
     *           allOf={
     *                @OA\Schema(
     *                  @OA\Property(property="code",type="integer",description="错误码",default="404"),
     *                  @OA\Property(property="msg",type="string", description="错误提示",default="页面不存在"),
     *                  @OA\Property(property="data",type="object")
     *                )
     *           }
     *         )
     *     )
     * )
     */
    public function indexAction()
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        Log::info(__METHOD__ . ' 请求地址不存在', ['uri' => $uri]);
        return $this->responseError('404', '页面不存在', new \stdClass(), __METHOD__);
    }


}