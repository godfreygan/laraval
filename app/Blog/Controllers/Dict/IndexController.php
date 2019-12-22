<?php

namespace App\Blog\Controllers\Dict;

use App\Blog\Controllers\Base;
use App\Blog\Library\Exceptions\ServiceException;
use App\Blog\Library\Util\DictUtil;
use App\Blog\Modules\Dict\Dict as DictModule;
use App\Util\ValidatorUtil;

/**
 * 字典相关接口
 *
 */
class IndexController extends Base
{
    /**
     * @OA\Post(
     *      path="/rpc.php?dict_index_getDictInfo",
     *      tags={"dict"},
     *      summary="通过字典组批量获取字典信息(原样输出)",
     *      operationId="dict_index_getdictinfo",
     *      description="",
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *                  mediaType="application/json",
     *           @OA\Schema(
     *                  allOf={
     *                     @OA\Schema(ref="#/components/schemas/request_rpc_common_param"),
     *                     @OA\Schema(@OA\Property(property="params",type="array",@OA\Items(ref="#/components/schemas/dict_info_request"))),
     *                     @OA\Schema(@OA\Property(property="method",type="string",example="Dict\Index.getDictInfo")),
     *                 }
     *           )
     *         )
     *     ),
     *      @OA\Response(
     *          response="200",
     *          description="请求成功",
     *          @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/apiprotocol"),
     *                 @OA\Schema(
     *                     required={"data"},
     *                     ref="#/components/schemas/dict_info_response_list"
     *                 )
     *             }
     *          )
     *      )
     * )
     */
    public function getDictInfoAction($param = [])
    {
        $rulesMap = [
            'type' => ['required|array', '字典组类型']
        ];
        list($rules, $message) = ValidatorUtil::formatRule($rulesMap);
        try {
            $data = $this->validate($param, $rules, $message, TRUE, __METHOD__);
            $ret  = DictModule::getDictInfoRedisByTypes($data['type']);
            return $this->responseSuccess($ret, __METHOD__);
        } catch (ServiceException $e) {
            $this->log(__METHOD__, $e->getCode(), $e->getMessage());
            return $this->responseError($e->getCode(), $e->getMessage(), '', __METHOD__);
        }
    }

    /**
     * @OA\Post(
     *      path="/rpc.php?dict_index_getDict",
     *      tags={"dict"},
     *      summary="通过字典组批量获取字典信息(含子集)",
     *      operationId="dict_index_getdict",
     *      description="",
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *                  mediaType="application/json",
     *           @OA\Schema(
     *                  allOf={
     *                     @OA\Schema(ref="#/components/schemas/request_rpc_common_param"),
     *                     @OA\Schema(@OA\Property(property="params",type="array",@OA\Items(ref="#/components/schemas/dict_info_request"))),
     *                     @OA\Schema(@OA\Property(property="method",type="string",example="Dict\Index.getDict")),
     *                 }
     *           )
     *         )
     *     ),
     *      @OA\Response(
     *          response="200",
     *          description="请求成功",
     *          @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/apiprotocol"),
     *                 @OA\Schema(
     *                     required={"data"},
     *                     ref="#/components/schemas/dict_info_response_list_son"
     *                 )
     *             }
     *          )
     *      )
     * )
     */
    public function getDictAction($param = [])
    {
        $rulesMap = [
            'type' => ['required|array', '字典组类型']
        ];
        list($rules, $message) = ValidatorUtil::formatRule($rulesMap);
        try {
            $data = $this->validate($param, $rules, $message, TRUE, __METHOD__);
            $ret  = DictModule::getDictInfoRedisByTypes($data['type']);
            return $this->responseSuccess(DictUtil::formatArr($ret, 'parent_code', 'code', 'subset'), __METHOD__);
        } catch (ServiceException $e) {
            $this->log(__METHOD__, $e->getCode(), $e->getMessage());
            return $this->responseError($e->getCode(), $e->getMessage(), '', __METHOD__);
        }
    }

}