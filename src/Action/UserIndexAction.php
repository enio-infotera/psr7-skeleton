<?php

namespace App\Action;

use App\Domain\User\UserService;
use App\Http\HtmlResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class UserIndexAction implements ActionInterface
{
    /**
     * @var HtmlResponder
     */
    private $responder;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * Constructor.
     *
     * @param HtmlResponder $responder The responder
     * @param UserService $userService The user service
     */
    public function __construct(HtmlResponder $responder, UserService $userService)
    {
        $this->responder = $responder;
        $this->userService = $userService;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     *
     * @return ResponseInterface The resonse
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $viewData = [
            'users' => $this->userService->findAllUsers(),
        ];

        return $this->responder->render('User/user-index.twig', $viewData);
    }
}
