<?php

namespace RotHub\Hyperf\Middleware;

use Hyperf\HttpMessage\Server\Request\Parser;
use Hyperf\HttpMessage\Server\Response;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FormatResponseMiddleware implements MiddlewareInterface
{
    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if ($response instanceof Response) {
            $contentType = $this->getContentType($response);
            $data = $response->getBody()->getContents();

            $parser = new Parser();
            if ($parser->has($contentType)) {
                $data = $parser->parse($data, $contentType);

                $response = $this->setResponse($response, $data);
            } else if (empty($data)) {
                $response = $this->setResponse($response, $data);
            }
        }

        return $response;
    }

    protected function getContentType(Response $response): string
    {
        $rawContentType = $response->getContentType();

        if (($pos = strpos($rawContentType, ';')) === false) {
            $contentType = strtolower($rawContentType);
        } else {
            $contentType = strtolower(substr($rawContentType, 0, $pos));
        }

        return $contentType;
    }

    protected function setResponse(Response $response, mixed $data): ResponseInterface
    {
        $res['code'] = $response->getStatusCode();
        $res['message'] = $response->getReasonPhraseByCode($response->getStatusCode());
        $data and $res['data'] = $data;

        $response = $response->withBody(new SwooleStream(json_encode($res)));
        return Context::set(ResponseInterface::class, $response);
    }
}
