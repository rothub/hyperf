<?php

namespace RotHub\Hyperf\Exception;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;

class DefaultExceptionHandler extends ExceptionHandler
{
    /**
     * Handle the exception, and return the specified result.
     * 
     * @param \Throwable $throwable
     */
    public function handle(\Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        $res['code'] = 500;
        $res['message'] = $throwable->getMessage();
        config('app_debug', false) and $res['trace'] = $throwable->getTrace();

        return $response->withStatus(500)
            ->withBody(new SwooleStream(json_encode($res)));
    }

    /**
     * @inheritdoc
     */
    public function isValid(\Throwable $throwable): bool
    {
        return true;
    }
}
