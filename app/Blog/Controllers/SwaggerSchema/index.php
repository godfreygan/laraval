<?php


/**
 * @OA\Schema(
 *     schema="index_clearnredis",
 *     required={"group","key"},
 *      @OA\Property(property="group",type="string",description="Redis Group",default=""),
 *      @OA\Property(property="key",type="string",description="Redis Key 多个以英文逗号分隔",default=""),
 * ),
 *
 *
 * */