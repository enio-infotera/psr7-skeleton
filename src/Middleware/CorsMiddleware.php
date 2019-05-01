<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * CORS preflight middleware.
 */
final class CorsMiddleware implements MiddlewareInterface
{
    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface The response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (PHP_SAPI === 'cli' || $request->getMethod() !== 'OPTIONS') {
            return $handler->handle($request);
        }

        $response = $handler->handle($request);

        /** @var ResponseInterface $response */
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');

        $response = $response->withHeader(
            'Access-Control-Allow-Methods',
            $request->getHeaderLine('Access-Control-Request-Method')
        );

        $response = $response->withHeader(
            'Access-Control-Allow-Headers',
            $request->getHeaderLine('Access-Control-Request-Headers')
        );

        $response = $response->withStatus(200);

        return $response;
    }
}
