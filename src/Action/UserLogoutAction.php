<?php

namespace App\Action;

use App\Domain\User\Auth;
use App\Http\RouterUrl;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class UserLogoutAction implements ActionInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var RouterUrl
     */
    private $routerUrl;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory The  response factory
     * @param RouterUrl $routerUrl The routerUrl
     * @param Auth $auth The user auth
     */
    public function __construct(ResponseFactoryInterface $responseFactory, RouterUrl $routerUrl, Auth $auth)
    {
        $this->responseFactory = $responseFactory;
        $this->routerUrl = $routerUrl;
        $this->auth = $auth;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $this->auth->logout();

        return $this->responseFactory->createResponse()
            ->withStatus(StatusCode::STATUS_FOUND)
            ->withHeader('Location', $this->routerUrl->pathFor('login'));
    }
}
