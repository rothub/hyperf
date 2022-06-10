<?php

namespace RotHub\Hyperf\Request;

use RotHub\Hyperf\Request\Rule;
use RotHub\Hyperf\Request\FormRequest;

class PageRequest extends FormRequest
{
    /**
     * @inheritdoc
     */
    public function defaultRules(): array
    {
        return Rule::page();
    }
}
