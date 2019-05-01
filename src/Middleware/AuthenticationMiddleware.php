<?php

namespace App\Middleware;

use App\Domain\User\Auth;
use App\Http\RouterUrl;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware.
 */
final class AuthenticationMiddleware implements MiddlewareInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var RouterUrl */
    private $routerUrl;

    /** @var Auth */
    private $auth;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory The repository factory
     * @param RouterUrl $routerUrl The repository url
     * @param Auth $auth The auth
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        RouterUrl $routerUrl,
        Auth $auth
    ) {
        $this->responseFactory = $responseFactory;
        $this->routerUrl = $routerUrl;
        $this->auth = $auth;
    }

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
        // If user has auth, use the request handler to continue to the next
        // middleware and ultimately reach your route callable
        if ($this->auth->check()) {
            return $handler->handle($request);
        }

        // If user does not have auth, redirect to login page
        $url = $this->routerUrl->pathFor('login');

        return $this->responseFactory->createResponse()
            ->withStatus(StatusCode::STATUS_FOUND)
            ->withHeader('Location', $url);
    }
}
