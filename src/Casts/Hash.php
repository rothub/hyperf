<?php

namespace RotHub\Hyperf\Casts;

use Hyperf\Contract\CastsAttributes;

class Hash implements CastsAttributes
{
    /**
     * @var string 哈希算法.
     */
    protected $algorithm;

    /**
     * @inheritdoc
     */
    public function __construct($algorithm = null)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * @inheritdoc
     */
    public function get($model, $key, $value, $attributes)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function set($model, $key, $value, $attributes)
    {
        return is_null($this->algorithm)
            ? bcrypt($value)
            : hash($this->algorithm, $value);
    }
}
