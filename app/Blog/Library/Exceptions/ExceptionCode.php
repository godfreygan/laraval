<?php

return [
    ####################################系统错误#############################################
    'unauthorized'                      => ['未授权访问', 500],
    'notfound'                          => ['页面不存在', 404],
    'system_error'                      => ['系统异常', 99000000],
    'invalid_argument'                  => ['参数校验错误', 99000001],
    'rpc_service_error'                 => ['调用RPC服务异常', 99000002],
    'database_error'                    => ['数据操作异常', 99000003],
    'rpc_name_error'                    => ['调用RPC服务名错误', 99000004],
    'invalid_business_id'               => ['非法的调用业务ID', 99000005],
    'bcode_error'                       => ['非法的业务code', 99000006],
    'please_not_repeat_commit'          => ['重复提交', 99000007],
    'api_only_dev_open'                 => ['此api仅允许在开发环境调用，还未对外开放', 99000008],
    "expired_token"                     => ['过期token值', 99000009],
    "no_find_token"                     => ['没有找到token', 99000010],
    "invalid_token"                     => ['非法的token值', 99000011],
    'api_deny_production_exec'          => ['此api不允许生产环境调用', 99000012],
    "invalid_login_token"               => ['该Token不存在或已过期', 99000013],
    "data_update_failed"                => ['数据更新失败', 99000014],
    'database_exception'                => ['数据库服务异常', 1045],
    'object_not_fund'                   => ['操作对象不存在', 9999998],
    'handel_fail'                       => ['操作失败', 9999999],
    ####################################用户相关10000000~19999999#############################
    'user_not_found'                    => ['用户不存在', 10000001],
    'unionlogin_bundle_failed'          => ['用户绑定失败', 10000002],
    'unionlogin_unbundle_failed'        => ['用户解绑失败', 10000003],
    'signature_auth_failed'             => ['签名验证失败', 10000004],
    'invalid_mobile_number'             => ['非法的手机号', 10000005],
    'user_bind_third_account_done'      => ['用户已经绑定第三方账号', 10000006],
    'third_account_bind_user_done'      => ['第三方账号已经绑定用户', 10000007],
    'third_bind_login_scene_type_error' => ['第三方账号绑定登录场景状态错误', 10000008],
    'third_account_type_error'          => ['第三方账号类型错误', 10000009],
    'user_already_exist'                => ['用户已存在', 10000010],
    'user_login_failed'                 => ['用户名/密码错误', 10000011],
    'invalid_sms_code'                  => ['短信验证码错误', 10000012],
    'bind_type_error'                   => ['绑定类型错误', 10000013],
    'user_address_limit'                => ['最多能保存20个有效收货地址', 10000014],
    'user_login_no_type'                => ['不能确定登录类型', 10000015],
    'user_login_is_lock'                => ['登录状态被锁定', 10000016],
    #user-token 相关
    'user_token_expire'                 => ['登陆态过期', 10001001],
    'user_token_no_match_user'          => ['登录态与用户信息不匹配', 10001002],
    'user_token_no_match_channel'       => ['登录态与登录渠道不匹配', 10001003],

    # 外部服务异常
    'user_service_error'                => ['用户服务异常', 20003000],
    'blog_service_error'                => ['博客服务异常', 20003001],

    #####################################字典相关#################################
    'dict_data_not_exit'                => ['字典暂无相关数据', 5000010000],
];

