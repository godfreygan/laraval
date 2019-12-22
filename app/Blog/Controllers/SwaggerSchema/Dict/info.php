<?php

/**
 * @OA\Schema(
 *     schema="dict_info_request",
 *     required={"type"},
 *     @OA\Property(property="type",type="array",description="字典组", @OA\Items(type="string")),
 * ),
 * @OA\Schema(
 *     schema="dict_info_response_list",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="sex",
 *             description="字典组",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/dict_info_list")
 *         ),
 *     )
 * ),
 * @OA\Schema(
 *     schema="dict_info_list",
 *     @OA\Property(property="type",type="string",description="字典组",example="sex"),
 *     @OA\Property(property="parent_code",type="string",description="父级CODE",example="0"),
 *     @OA\Property(property="code",type="string",description="字典CODE",example="0"),
 *     @OA\Property(property="value",type="string",description="字典CODE对应值",example="女"),
 *     @OA\Property(property="remark",type="string",description="备注",example="性别"),
 * ),
 * @OA\Schema(
 *     schema="dict_info_response_list_son",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="sex",
 *             description="字典组",
 *             type="object",
 *             @OA\Property(property="type",type="string",description="字典组",example="sex"),
 *             @OA\Property(property="parent_code",type="string",description="父级CODE",example="0"),
 *             @OA\Property(property="code",type="string",description="字典CODE",example="0"),
 *             @OA\Property(property="value",type="string",description="字典CODE对应值",example="女"),
 *             @OA\Property(property="remark",type="string",description="备注",example="性别"),
 *             @OA\Property(property="subset",type="array",description="子集",@OA\Items(ref="#/components/schemas/dict_info_list")),
 *         ),
 *     )
 * ),
 *
 * @OA\Schema(
 *     schema="dict_info_marketing_type_response",
 *     @OA\Property(
 *         property="list",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="type",type="string",description="字典组",example="sex"),
 *             @OA\Property(property="parent_code",type="string",description="父级CODE",example="0"),
 *             @OA\Property(property="code",type="string",description="字典CODE",example="0"),
 *             @OA\Property(property="value",type="string",description="字典CODE对应值",example="女"),
 *             @OA\Property(property="remark",type="string",description="备注",example="性别"),
 *         ),
 *     )
 * ),
 */