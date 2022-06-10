<?php

namespace RotHub\Hyperf\Casts;

use Hyperf\Contract\CastsAttributes;

class Email implements CastsAttributes
{
    /**
     * @inheritdoc
     */
    public function get($model, $key, $value, $attributes)
    {
        if ($value && is_string($value)) {
            return substr(strstr($value, '@', true), 0, 3) . '****' . strstr($value, '@');
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
