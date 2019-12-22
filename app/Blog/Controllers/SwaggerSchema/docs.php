<?php
/**
 *
 * @OA\Info(
 *   title="接口文档",
 *   description="",
 *   version="1.0.0",
 *   @OA\Contact(
 *     email="g54787652@gmail.com",
 *     name="godfrey.gan"
 *   ),
 *   termsOfService="http://swagger.io/terms/"
 * )
 *
 * @OA\OpenApi(
 *   @OA\Server(
 *       url="http://blog.dazhairen.com/",
 *       description="本机开发地址"
 *   ),
 *   @OA\ExternalDocumentation(
 *     description="更多项目信息见官网",
 *     url="https://swagger.io/about"
 *   )
 * )
 *
 * @OA\Tag(
 *   name="article",
 *   description="文章相关接口"
 * )
 *
 * @OA\Tag(
 *   name="admin",
 *   description="后台相关接口",
 * )
 *
 * @OA\Tag(
 *   name="other",
 *   description="其他相关接口",
 * )
 *
 * @OA\Schema(
 *      schema="apiprotocol",
 *      required={"code", "msg", "data"},
 *      @OA\Property(
 *          property="code",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="msg",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="object"
 *      )
 *  )
 *
 * @OA\Schema(
 *     schema="",
 *     required={"result"},
 *     @OA\Property(
 *          property="result",
 *          type="integer",
 *          format="int32",
 *          enum={"0:失败","1:成功"}
 *      )
 * )
 *
 * @OA\Schema(
 *     schema="apidatalist",
 *     title="返回分页列表",
 *     required={"list", "total", "page", "page_size"},
 *     @OA\Property(
 *         property="list",
 *         type="array",
 *         @OA\Items()
 *     ),
 *     @OA\Property(
 *         property="total",
 *         type="integer",
 *         format="int32",
 *         description="总记录数",
 *         example=18
 *     ),
 *     @OA\Property(
 *         property="page",
 *         type="integer",
 *         format="int32",
 *         description="当前页码",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="page_size",
 *         type="integer",
 *         format="int32",
 *         description="每页显示记录数",
 *         example=10
 *     )
 * ),
 *
 * RPC请求公共请求部分
 * @OA\Schema(
 *     schema="request_rpc_common_param",
 *     required={"jsonrpc","method","params","id"},
 *     @OA\Property(property="jsonrpc",type="string",description="指定JSON-RPC协议版本的字符串",default="2.0",example="2.0"),
 *     @OA\Property(property="method",type="string",description="调用方法名称的字符串",default="目录\\文件名.方法名",example="目录\\文件名.方法名"),
 *     @OA\Property(property="params",description="params"),
 *     @OA\Property(property="id",type="string",description="客户端的唯一标识id",default="SFGNGW-0343SD-GEQSOS-544",example="SFGNGW-0343SD-GEQSOS-544"),
 * )
 *
 */
