<?php

namespace RotHub\Hyperf\Model;

class Shorten
{
    /**
     * 模糊搜索.
     *
     * @param array $data 数据.
     * @param string|array $columns 字段 => 数据库字段名.
     * @param string $key 键名 => 请求数据键名.
     * @return array
     */
    public static function like(
        array $data,
        string|array $columns,
        string $key = 'search'
    ): array {
        if (isset($data[$key]) && !empty($data[$key])) {
            $keyword = '%' . $data[$key] . '%';

            $callback = function ($query) use ($columns, $keyword) {
                $query->where(function ($query) use ($columns, $keyword) {
                    foreach ((array) $columns as $column) {
                        $query->orWhere($column, 'like', $keyword);
                    }
                });
            };

            return [true, $callback, null];
        }

        return [false, null, null];
    }

    /**
     * WHEN.
     *
     * @param array $data 数据.
     * @param string $column 字段 => 数据库字段名.
     * @param string $operator 操作符.
     * @param string $key 键名 => 请求数据键名.
     * @return array
     */
    public static function when(
        array $data,
        string $column,
        string $operator = '=',
        string $key = null
    ): array {
        if (is_null($key)) {
            $has = strstr($column, '.');

            $key = $has === false ? $column : substr($has, 1);
        }

        if (isset($data[$key]) && !static::isEmpty($data[$key])) {
            $value = $data[$key];

            $callback = function ($query) use ($column, $operator, $value) {
                if (is_array($value)) {
                    return $query->whereIn($column, $value);
                } else {
                    return $query->where($column, $operator, $value);
                }
            };

            return [true, $callback, null];
        }

        return [false, null, null];
    }

    /**
     * 排序.
     *
     * @param array $data 数据.
     * @param string $column 字段 => 数据库字段名.
     * @param string $key 键名 => 请求数据键名.
     * @param string $separator 分隔符.
     * @return array
     */
    public static function sort(
        array $data,
        string $column = null,
        string $key = null,
        string $separator = ':'
    ): array {
        is_null($key) and $key = config('rothub.SORT_AT');

        if (isset($data[$key]) && !static::isEmpty($data[$key])) {
            $orders = explode($separator, $data[$key]);
            is_null($column) or $order[0] = $column;

            $callback = function ($query) use ($orders) {
                return $query->orderBy(...$orders);
            };

            return [true, $callback, null];
        }

        return [false, null, null];
    }

    /**
     * 分页.
     *
     * @param array $data 数据.
     * @param array $columns 字段.
     * @return array
     */
    public static function page(array $data, array $columns = ['*']): array
    {
        $page = $data[config('rothub.PAGE_NO_AT')] ?? 1;
        $size = $data[config('rothub.PAGE_SIZE_AT')] ?? 10;

        return [$size, $columns, '', $page];
    }

    /**
     * 添加软删除条件.
     *
     * @param mixed $join 连接.
     * @param string $model 模型.
     * @param string $alias 别名.
     * @return void
     */
    public static function addSoftDelete(mixed &$join, string $model, string $alias): void
    {
        $instance = new $model;

        $column = $alias . '.' . $instance->domainAt();
        $join->where($column, $instance->domain());

        $column = $alias . '.' . $instance->getDeletedAtColumn();
        $join->where($column, '<>', $instance->deletedValue);
    }

    /**
     * 是否空.
     *
     * @param mixed $value 值.
     * @return bool
     */
    public static function isEmpty(mixed $value): bool
    {
        return $value === [] || $value === null || is_string($value) && trim($value) === '';
    }
}
