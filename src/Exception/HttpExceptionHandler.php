<?php

namespace RotHub\Hyperf\Exception;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;

class HttpExceptionHandler extends ExceptionHandler
{
    /**
     * Handle the exception, and return the specified result.
     * 
     * @param HttpException $throwable
     */
    public function handle(\Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        $res['code'] = $throwable->getStatusCode();
        $res['message'] = $throwable->getMessage();
        config('app_debug', false) and $res['trace'] = $throwable->getTrace();

        return $response->withStatus($throwable->getStatusCode())
            ->withBody(new SwooleStream(json_encode($res)));
    }

    /**
     * @inheritdoc
     */
    public function isValid(\Throwable $throwable): bool
    {
        return $throwable instanceof HttpException;
    }
}
