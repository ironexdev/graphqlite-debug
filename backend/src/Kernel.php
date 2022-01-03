<?php

namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use TheCodingMachine\GraphQLite\Http\WebonyxGraphqlMiddleware;
use Tuupola\Middleware\CorsMiddleware;

class Kernel
{
    public function __construct(
        ServerRequestInterface $request,
        ResponseInterface $defaultResponse,
        CorsMiddleware $corsMiddleware,
        WebonyxGraphqlMiddleware $graphQLMiddleware
    )
    {
        $middlewareStack = new MiddlewareStack(
            $defaultResponse->withStatus(404), // default/fallback response
            $corsMiddleware,
            $graphQLMiddleware
        );

        $response = $middlewareStack->handle($request);

        $this->sendResponse($response->getStatusCode(), $response->getHeaders(), $response->getBody());
    }

    /**
     * @param int $code
     * @param array $headers
     * @param StreamInterface $body
     */
    private function sendResponse(int $code, array $headers, StreamInterface $body)
    {
        http_response_code($code);

        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                header(sprintf("%s: %s", $name, $value), false);
            }
        }

        echo $body;
    }
}
