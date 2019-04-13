<?php

namespace App\Action;

use App\Domain\User\Auth;
use App\Domain\User\UserService;
use Cake\Chronos\Chronos;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
class HomeLoadAction implements ActionInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param Auth $auth
     * @param UserService $userService
     */
    public function __construct(ResponseFactoryInterface $responseFactory, Auth $auth, UserService $userService)
    {
        $this->responseFactory = $responseFactory;
        $this->auth = $auth;
        $this->userService = $userService;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request the request
     *
     * @return ResponseInterface the response
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $userId = $this->auth->getUserId();
        $user = $this->userService->getUserById($userId);

        $result = [
            'message' => __('Loaded successfully!'),
            'now' => Chronos::now()->toDateTimeString(),
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
            ],
        ];

        $response = $this->responseFactory->createResponse();
        $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');
        $response->getBody()->write(json_encode($result) ?: '');

        return $response;
    }
}
