<?php


/**
 *  @OA\Schema(
 *     schema="index_clearnredis",
 *     required={"group","key"},
 *      @OA\Property(property="group",type="string",description="Redis Group",default=""),
 *      @OA\Property(property="key",type="string",description="Redis Key 多个以英文逗号分隔",default=""),
 * )
 *
 * ----------------------------- 发Kafka消息接口 -----------------------------
 *  @OA\Schema(
 *     schema="index_sendkafkamessagerequest",
 *     required={"queue","type","data"},
 *      @OA\Property(property="queue",type="string",description="producer config-key",default=""),
 *      @OA\Property(property="type",type="string",description="event type",default=""),
 *      @OA\Property(property="data",type="string",description="实际消息内容，json格式",default=""),
 *      @OA\Property(property="id",type="string",description="id，选填",default=""),
 * )
 *
 *
 * */