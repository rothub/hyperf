<?php

namespace RotHub\Hyperf\Exception;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;

class ValidationExceptionHandler extends ExceptionHandler
{
    /**
     * Handle the exception, and return the specified result.
     * 
     * @param ValidationException $throwable
     */
    public function handle(\Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        $message = $throwable->validator->errors()->first();

        $res['code'] = $throwable->status;
        $res['message'] = $message;
        config('app_debug', false) and $res['trace'] = $throwable->getTrace();

        return $response->withStatus($throwable->status)
            ->withBody(new SwooleStream(json_encode($res)));
    }

    /**
     * @inheritdoc
     */
    public function isValid(\Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
