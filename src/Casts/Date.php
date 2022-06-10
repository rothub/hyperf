<?php

namespace RotHub\Hyperf\Casts;

use Hyperf\Contract\CastsAttributes;

class Date implements CastsAttributes
{
    /**
     * @var string 格式化.
     */
    protected $format;
    /**
     * @var string 空值.
     */
    protected $nil;
    /**
     * @var string 默认.
     */
    protected $default;

    /**
     * @inheritdoc
     */
    public function __construct(
        $format = null,
        $nil = null,
        $default = ''
    ) {
        is_null($format) and $format = config('rothub.DATETIME_FORMAT');
        is_null($nil) and $nil = config('rothub.DATETIME_NIL');

        $this->format = $format;
        $this->nil = $nil;
        $this->default = $default;
    }

    /**
     * @inheritdoc
     */
    public function get($model, $key, $value, $attributes)
    {
        return $this->cast($value);
    }

    /**
     * @inheritdoc
     */
    public function set($model, $key, $value, $attributes)
    {
        return $this->cast($value);
    }

    /**
     * 转换.
     *
     * @param mixed $value 值.
     * @return mixed
     */
    protected function cast(mixed $value): mixed
    {
        $date = (new \DateTime($value))->format($this->format);

        return $date === $this->nil ? $this->default : $value;
    }
}
