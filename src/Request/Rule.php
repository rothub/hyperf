<?php

namespace RotHub\Hyperf\Request;

use Hyperf\Validation\Rule as HyperfRule;
use RotHub\Hyperf\Model\Model;

class Rule
{
    /**
     * 数组.
     * 
     * @param bool $required 是否必须.
     * @return string
     */
    public static function array(bool $required = false): string
    {
        $str = static::required($required);

        return $str . 'array';
    }

    /**
     * 字符串.
     * 
     * @param int $max 最大长度.
     * @param bool $required 是否必须.
     * @return string
     */
    public static function string(int $max = 191, bool $required = false): string
    {
        $str = static::required($required);

        return $str . 'string|max:' . $max;
    }

    /**
     * 整型.
     * 
     * @param int $max 最大值.
     * @param bool $required 是否必须.
     * @return string
     */
    public static function int(int $max = 999999, bool $required = false): string
    {
        $str = static::required($required);

        return $str . 'integer|numeric|min:0|max:' . $max;
    }

    /**
     * 浮点数.
     * 
     * @param int $scale 小数位数.
     * @param bool $required 是否必须.
     * @return string
     */
    public static function decimal(int $scale = 2, bool $required = false): string
    {
        $str = static::required($required);

        return $str . 'numeric|min:0|regex:/^\d{1,18}(\.\d{1,' . $scale . '})?$/';
    }

    /**
     * 日期格式.
     * 
     * @param string $format 格式.
     * @param bool $required 是否必须.
     * @return string
     */
    public static function date(string $format = 'Y-m-d H:i:s', bool $required = false): string
    {
        $str = static::required($required);

        return $str . 'date_format:"' . $format . '"';
    }

    /**
     * 网址.
     * 
     * @param bool $required 是否必须.
     * @return string
     */
    public static function url(bool $required = false): string
    {
        $str = static::required($required);

        return $str . 'url|max:191';
    }

    /**
     * 文件.
     * 
     * @param bool $required 是否必须.
     * @return string
     */
    public static function file(bool $required = false): string
    {
        $str = static::required($required);

        return $str . 'file';
    }

    /**
     * 图片.
     * 
     * @param bool $required 是否必须.
     * @return string
     */
    public static function image(bool $required = false): string
    {
        $str = static::required($required);

        return $str . 'image';
    }

    /**
     * 邮箱.
     * 
     * @param bool $required 是否必须.
     * @return string
     */
    public static function email(bool $required = false): string
    {
        $str = static::required($required);

        return $str . 'email|max:64';
    }

    /**
     * 手机.
     * 
     * @param bool $required 是否必须.
     * @return string
     */
    public static function phone(bool $required = false): string
    {
        $str = static::required($required);

        return $str . 'string|regex:/^1[3456789]{1}\d{9}$/';
    }

    /**
     * 存在于.
     * 
     * @param array $values 值数组.
     * @param bool $required 是否必须.
     * @return array
     */
    public static function in(array $values, bool $required = false): array
    {
        $required and $rules[] = 'required';
        $rules[] = HyperfRule::in($values);

        return $rules;
    }

    /**
     * ID规则.
     * 
     * @return array
     */
    public static function id(): array
    {
        return ['id' => 'required|string'];
    }

    /**
     * 删除规则.
     * 
     * @return array
     */
    public static function delete(): array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'distinct|string',
        ];
    }

    /**
     * 分页规则.
     * 
     * @return array
     */
    public static function page(): array
    {
        return [
            'search' => 'string|max:64',
            'status' =>  HyperfRule::in(Model::STATUS_ALL),
            config('rothub.SORT_AT') => 'string|ends_with:asc,desc',
            config('rothub.PAGE_NO_AT') => 'integer|numeric|min:1',
            config('rothub.PAGE_SIZE_AT') => 'integer|numeric|min:1|max:100',
        ];
    }

    /**
     * 账号规则.
     * 
     * @return string
     */
    public static function username(): string
    {
        return 'required|regex:/^[a-zA-Z0-9]+$/u|between:3,8';
    }

    /**
     * 密码规则.
     * 
     * @return string
     */
    public static function password(): string
    {
        return 'required|regex:/^[a-zA-Z0-9_-]+$/u|between:8,16';
    }

    /**
     * 是否必须.
     * 
     * @param bool $required 是否必须.
     * @return string
     */
    protected static function required(bool $required = false): string
    {
        return $required ? 'required|' : '';
    }
}
