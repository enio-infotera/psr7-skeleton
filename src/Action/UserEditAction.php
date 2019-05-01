<?php

namespace App\Action;

use App\Domain\User\UserService;
use App\Http\HtmlResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class UserEditAction implements ActionInterface
{
    /** @var HtmlResponder */
    private $responder;

    /** @var UserService */
    private $userService;

    /**
     * Constructor.
     *
     * @param HtmlResponder $responder The responder
     * @param UserService $userService The user service
     */
    public function __construct(
        HtmlResponder $responder,
        UserService $userService
    ) {
        $this->responder = $responder;
        $this->userService = $userService;
    }

    /**
     * Action.
     *
     * @param array $args
     * @param ServerRequestInterface $request the request
     *
     * @return ResponseInterface the response
     */
    public function __invoke(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $userId = (int)$args['id'];

        $user = $this->userService->getUserById($userId);

        $viewData = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
        ];

        return $this->responder->render('User/user-edit.twig', $viewData);
    }
}
