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
    protected $userService;

    /**
     * Constructor.
     *
     * @param HtmlResponder $responder
     * @param UserService $userService
     */
    public function __construct(HtmlResponder $responder, UserService $userService)
    {
        $this->responder = $responder;
        $this->userService = $userService;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $viewData = [
            'users' => $this->userService->findAllUsers(),
        ];

        return $this->responder->render('User/user-index.twig', $viewData);
    }
}
