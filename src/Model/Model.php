<?php

namespace RotHub\Hyperf\Model;

use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Db;
use RotHub\Hyperf\Model\Eloquent;

class Model extends Eloquent
{
    /**
     * 业务区分.
     */
    const DOMAIN = '';

    /**
     * 状态: 关闭.
     */
    const STATUS_OFF = 0;
    /**
     * 状态: 开启.
     */
    const STATUS_ON = 1;
    /**
     * 状态: 全部.
     */
    const STATUS_ALL = [
        self::STATUS_OFF,
        self::STATUS_ON,
    ];

    /**
     * 是否使用.
     *
     * @param string $column 字段.
     * @param string|array $value 值.
     * @param string $id 过滤ID.
     * @return bool
     */
    public static function isUsed(string $column, string|array $value, string $id = null): bool
    {
        is_array($value) or $value = [$value];

        $model = new static();
        $query = $model->whereIn($column, $value);
        $id and $query->where($model->getKeyName(), '<>', $id);
        return $query->exists();
    }

    /**
     * 冻结/解冻.
     *
     * @param array $ids ID.
     * @param bool $frozen 冻结.
     * @return bool
     */
    public static function freeze(array $ids, bool $frozen): bool
    {
        $model = new static();

        return $model->whereIn($model->getKeyName(), $ids)
            ->where('status', $frozen ? static::STATUS_ON : static::STATUS_OFF)
            ->update(['status' => $frozen ? static::STATUS_OFF : static::STATUS_ON]);
    }

    /**
     * 批量插入.
     *
     * @param array $rows 数据.
     * @return bool
     */
    public static function batchInsert(array $rows): bool
    {
        $model = new static();

        $columns = array_keys($model->columns());
        $time = date($model->getDateFormat());
        $userid = $model->userid();

        $model->setHidden([]);
        $model->setAttributes([
            $model->domainAt() => static::DOMAIN,
            'created_at' => $time,
            'updated_at' => $time,
            'created_by' => $userid,
            'updated_by' => $userid,
        ], $columns);

        foreach ($rows as &$row) {
            if ($row instanceof static) {
                $row->setHidden([]);
                $row = $row->toArray();
            } else if (is_array($row)) {
            } else {
                $row = [];
            }

            $clone = clone $model;
            $clone->setAttributes($row, $columns);
            $row = $clone->getAttributes();
        }

        return DB::table($model->table())->insert($rows);
    }

    /**
     * 设置属性.
     *
     * @param array $attributes 数据.
     * @param array $columns 列名.
     * @return static
     */
    public function setAttributes(array $attributes, array $columns = null): static
    {
        is_null($columns) and $columns = array_keys(static::columns());

        $has = [];
        foreach ($attributes as $key => $value) {
            if (in_array($key, $columns)) {
                $has[$key] = $value;
            }
        }

        $this->fill($has);

        return $this;
    }

    /**
     * 得到表名.
     *
     * @return string
     */
    public static function table(): string
    {
        return (new static())->getTable();
    }

    /**
     * 得到表别名.
     *
     * @param string $alias 别名.
     * @return string
     */
    public static function alias(string $alias): string
    {
        return static::table() . ' as ' . $alias;
    }

    /**
     * 业务区分.
     *
     * @return string
     */
    public static function domain(): string
    {
        return static::DOMAIN;
    }

    /**
     * 业务区分字段.
     *
     * @return string
     */
    public static function domainAt(): string
    {
        return config('DOMAIN_AT', 'domain');
    }

    /**
     * @inheritdoc
     */
    public function creating()
    {
        $domain = static::domainAt();
        $userid = $this->userid();

        $this->$domain = static::DOMAIN;
        $this->created_by = $userid;
        $this->updated_by = $userid;
    }

    /**
     * @inheritdoc
     */
    public function saving()
    {
        $userid = $this->userid();

        $this->updated_by = $userid;
    }

    /**
     * @inheritdoc
     */
    public function deleting()
    {
        $userid = $this->userid();

        $this->updated_by = $userid;
    }

    /**
     * 表字段.
     *
     * @return Doctrine\DBAL\Schema\Column[]
     */
    public static function columns(): array
    {
        return Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableColumns(static::table());
    }

    /**
     * 用户 ID.
     *
     * @return string
     */
    protected static function userid(): string
    {
        return '';
    }
}
