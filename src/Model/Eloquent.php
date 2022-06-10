<?php

namespace RotHub\Hyperf\Model;

use Hyperf\DbConnection\Model\Model as HyperfModel;

class Eloquent extends HyperfModel
{
    /**
     * 软删除字段.
     */
    const DELETED_AT = 'status';

    /**
     * @var bool 自动维护时间戳.
     */
    public $timestamps = true;
    /**
     * @var string 日期列的存储格式.
     */
    protected $dateFormat = 'Y-m-d H:i:s';
    /**
     * @var array 不可批量赋值的属性.
     */
    protected $guarded = [];
    /**
     * @var array 模型的默认属性值.
     */
    protected $attributes = [
        'status' => 1,
    ];
}
