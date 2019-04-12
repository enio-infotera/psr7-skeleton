<?php

namespace App\Middleware;

use App\Domain\User\Auth;
use App\Utility\RouterUrl;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use League\Route\Router;

/**
 * Middleware.
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var RouterUrl
     */
    protected $routerUrl;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param Router $router
     * @param RouterUrl $routerUrl
     * @param Auth $auth
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        Router $router,
        RouterUrl $routerUrl,
        Auth $auth
    ) {
        $this->responseFactory = $responseFactory;
        $this->router = $router;
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
