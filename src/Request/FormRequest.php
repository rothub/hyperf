<?php

namespace RotHub\Hyperf\Request;

use Hyperf\Validation\Request\FormRequest as HyperfRequest;

abstract class FormRequest extends HyperfRequest
{
    /**
     * @inheritdoc
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * 默认规则.
     *
     * @return array
     */
    public function defaultRules(): array
    {
        return [];
    }

    /**
     * 全部规则.
     *
     * @return array
     */
    public function allRules(): array
    {
        $rules1 = call_user_func_array([$this, 'rules'], []);
        $rules2 = call_user_func_array([$this, 'defaultRules'], []);

        return array_merge($rules1, $rules2);
    }

    /**
     * @inheritdoc
     */
    protected function getRules()
    {
        $rules1 = call_user_func_array([$this, 'rules'], []);
        $rules2 = call_user_func_array([$this, 'defaultRules'], []);
        $rules = array_merge($rules1, $rules2);

        foreach ($rules as &$rule) {
            if (is_string($rule)) {
                $rule = 'bail|' . $rule;
            } else if (is_array($rule)) {
                array_unshift($rule, 'bail');
            } else {
                $rule = ['bail', $rule];
            }
        }

        $scene = $this->getScene();
        if ($scene && isset($this->scenes[$scene]) && is_array($this->scenes[$scene])) {
            $newRules = [];
            foreach ($this->scenes[$scene] as $field) {
                if (array_key_exists($field, $rules)) {
                    $newRules[$field] = $rules[$field];
                }
            }
            return $newRules;
        }
        return $rules;
    }
}
