<?php

namespace RotHub\Hyperf\Exception;

use Hyperf\Server\Exception\ServerException;

class Error extends ServerException
{
    /**
     * 异常信息: 默认错误信息.
     */
    const FAIL = '操作失败.';

    /**
     * 失败.
     * 
     * @param string $message 错误信息.
     * @param int $code 错误编码.
     * @param int $status 状态编码.
     * @return void
     */
    public static function fail(
        string $message = self::FAIL,
        int $code = 400,
        int $status = 400
    ): void {
        $res['code'] = $code;
        $res['message'] = $message;

        throw new static(json_encode($res), $status);
    }
}
