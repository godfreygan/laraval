<?php

return [
    ####################################系统错误#############################################
    'UNAUTHORIZED'                      => ['未授权访问', 500],
    'NOTFOUND'                          => ['页面不存在', 404],
    'SYSTEM_ERROR'                      => ['系统异常', 99000000],
    'INVALID_ARGUMENT'                  => ['参数校验错误', 99000001],
    'RPC_SERVICE_ERROR'                 => ['调用RPC服务异常', 99000002],
    'DATABASE_ERROR'                    => ['数据操作异常', 99000003],
    'RPC_NAME_ERROR'                    => ['调用RPC服务名错误', 99000004],
    'INVALID_BUSINESS_ID'               => ['非法的调用业务ID', 99000005],
    'BCODE_ERROR'                       => ['非法的业务CODE', 99000006],
    'PLEASE_NOT_REPEAT_COMMIT'          => ['重复提交', 99000007],
    'API_ONLY_DEV_OPEN'                 => ['此API仅允许在开发环境调用，还未对外开放', 99000008],
    "EXPIRED_TOKEN"                     => ['过期TOKEN值', 99000009],
    "NO_FIND_TOKEN"                     => ['没有找到TOKEN', 99000010],
    "INVALID_TOKEN"                     => ['非法的TOKEN值', 99000011],
    'API_DENY_PRODUCTION_EXEC'          => ['此API不允许生产环境调用', 99000012],
    "INVALID_LOGIN_TOKEN"               => ['该TOKEN不存在或已过期', 99000013],
    "DATA_UPDATE_FAILED"                => ['数据更新失败', 99000014],
    'DATABASE_EXCEPTION'                => ['数据库服务异常', 1045],
    'OBJECT_NOT_FUND'                   => ['操作对象不存在', 9999998],
    'HANDEL_FAIL'                       => ['操作失败', 9999999],
    ####################################用户相关10000000~19999999#############################
    'USER_NOT_FOUND'                    => ['用户不存在', 10000001],
    'UNIONLOGIN_BUNDLE_FAILED'          => ['用户绑定失败', 10000002],
    'UNIONLOGIN_UNBUNDLE_FAILED'        => ['用户解绑失败', 10000003],
    'SIGNATURE_AUTH_FAILED'             => ['签名验证失败', 10000004],
    'INVALID_MOBILE_NUMBER'             => ['非法的手机号', 10000005],
    'USER_BIND_THIRD_ACCOUNT_DONE'      => ['用户已经绑定第三方账号', 10000006],
    'THIRD_ACCOUNT_BIND_USER_DONE'      => ['第三方账号已经绑定用户', 10000007],
    'THIRD_BIND_LOGIN_SCENE_TYPE_ERROR' => ['第三方账号绑定登录场景状态错误', 10000008],
    'THIRD_ACCOUNT_TYPE_ERROR'          => ['第三方账号类型错误', 10000009],
    'USER_ALREADY_EXIST'                => ['用户已存在', 10000010],
    'USER_LOGIN_FAILED'                 => ['用户名/密码错误', 10000011],
    'INVALID_SMS_CODE'                  => ['短信验证码错误', 10000012],
    'BIND_TYPE_ERROR'                   => ['绑定类型错误', 10000013],
    'USER_ADDRESS_LIMIT'                => ['最多能保存20个有效收货地址', 10000014],
    'USER_LOGIN_NO_TYPE'                => ['不能确定登录类型', 10000015],
    'USER_LOGIN_IS_LOCK'                => ['登录状态被锁定', 10000016],
    #USER-TOKEN 相关
    'USER_TOKEN_EXPIRE'                 => ['登陆态过期', 10001001],
    'USER_TOKEN_NO_MATCH_USER'          => ['登录态与用户信息不匹配', 10001002],
    'USER_TOKEN_NO_MATCH_CHANNEL'       => ['登录态与登录渠道不匹配', 10001003],

    # 外部服务异常
    'USER_SERVICE_ERROR'                => ['用户服务异常', 20003000],
    'BLOG_SERVICE_ERROR'                => ['博客服务异常', 20003001],

    #####################################字典相关#################################
    'DICT_DATA_NOT_EXIT'                => ['字典暂无相关数据', 5000010000],
];

