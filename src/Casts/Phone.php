<?php

namespace RotHub\Hyperf\Casts;

use Hyperf\Contract\CastsAttributes;

class Phone implements CastsAttributes
{
    /**
     * @inheritdoc
     */
    public function get($model, $key, $value, $attributes)
    {
        if ($value && is_string($value)) {
            return substr_replace($value, '****', 3, 4);
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
