<?php

namespace App\Action;

use App\Domain\User\Auth;
use App\Domain\User\UserService;
use App\Http\JsonResponder;
use Cake\Chronos\Chronos;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class HomeLoadAction implements ActionInterface
{
    /**
     * @var JsonResponder
     */
    private $responder;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * Constructor.
     *
     * @param JsonResponder $responder The responder
     * @param Auth $auth The auth
     * @param UserService $userService The user service
     */
    public function __construct(JsonResponder $responder, Auth $auth, UserService $userService)
    {
        $this->responder = $responder;
        $this->auth = $auth;
        $this->userService = $userService;
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

        return $this->responder->encode($result);
    }
}
