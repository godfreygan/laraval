<?php
/**
 * 验证封装类
 */

/**
 *
 *
 * eg:
 * $aField = [
 * 'userid' => ['sometimes|required|integer|min:1|max:100|between:1,3','用户ID'],
 * 'username'     => ['required|string|min:1','用户名'],
 * ];
 * list($aField,$message) = ValidatorUtil::formatRule($aField);
 * $param = $this->validate($data, $aField, $message);
 *
 *
 */

namespace App\Util;

class ValidatorUtil
{

    private static $setRuleMap = [];

    private static function ruleMap()
    {
        $ruleMap          = [
            'required' => '%s必填',
            'integer'  => '%s必须是整形',
            'min'      => '%s最小值必须是%s',
            'max'      => '%s最大值必须是%s',
            'between'  => '%s不在有效范围内',
            'array'    => '%s必须是数组',
            'string'   => '%s必须是字符串',
            'in'       => '%s不在可选范围内',
            'regex'    => '%s格式不正确',
            'numeric'  => '%s必须是数值型',
            'distinct' => '%s不能重复',
            'date'     => '%s日期格式错误',
        ];
        self::$setRuleMap = array_merge(self::$setRuleMap, $ruleMap);
    }

    /**
     * 可自定义验证规则
     * @param $rule
     */
    public static function setRuleMap($rule)
    {
        self::$setRuleMap = $rule;
    }

    /**
     * 格式验证规则
     * @param $rules
     * @return array
     */
    public static function formatRule($rules)
    {
        self::ruleMap();//初始化map规则
        $resultRule = $resultMessage = [];
        foreach ($rules as $column => $ruleList) {
            list($rulesInfo, $columnCnName) = $ruleList;
            $resultRule [$column] = $rulesInfo;
            $resultMessage        = array_merge($resultMessage, self::getMessage($column, $columnCnName, $rulesInfo));
        }
        return [$resultRule, $resultMessage];
    }

    /**
     * 获取验证提示信息
     * @param $column
     * @param $column_name
     * @param $rules
     * @param $ruleMap
     * @return array
     */
    private static function getMessage($column, $columnCnName, $rulesInfo)
    {
        $message = [];
        $rules   = self::getRules($rulesInfo);
        foreach ($rules as $rule) {
            $ruleValue       = '';//分割规则:后的值
            $ruleExplodeBool = false;
            //确定是否以:分割的规则
            if (strpos($rule, ':') !== false) {
                list($rule, $ruleValue) = explode(':', $rule);
                $ruleExplodeBool = true;
            }
            //获取规则对应的提升信息
            $ruleMessage = isset(self::$setRuleMap[$rule]) ? self::$setRuleMap[$rule] : [];
            //如果没映射关系则跳过
            if (!$ruleMessage) {
                continue;
            }
            //有分割规则
            if ($ruleExplodeBool) {
                $message [$column . '.' . $rule] = sprintf($ruleMessage, $columnCnName, $ruleValue);
                continue;
            }
            $message [$column . '.' . $rule] = sprintf($ruleMessage, $columnCnName);
        }
        return $message;
    }

    /**
     *
     * @param $rules
     * @return array
     */
    private static function getRules($rules)
    {
        return explode('|', $rules);
    }
}