<?php

namespace App\Action;

use App\Domain\User\UserService;
use App\Http\HtmlResponder;
use Twig\Environment as Twig;
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
     * @var Twig
     */
    protected $twig;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Constructor.
     *
     * @param HtmlResponder $responder
     * @param Twig $twig
     * @param UserService $userService
     */
    public function __construct(HtmlResponder $responder, Twig $twig, UserService $userService)
    {
        $this->responder = $responder;
        $this->twig = $twig;
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
