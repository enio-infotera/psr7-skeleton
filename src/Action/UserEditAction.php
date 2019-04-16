<?php

namespace App\Action;

use App\Domain\User\UserService;
use App\Http\HtmlResponder;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment as Twig;

/**
 * Action.
 */
final class UserEditAction implements ActionInterface
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
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Constructor.
     *
     * @param HtmlResponder $responder
     * @param Twig $twig
     * @param SessionInterface $session
     * @param LoggerInterface $logger
     * @param UserService $userService
     */
    public function __construct(
        HtmlResponder $responder,
        Twig $twig,
        SessionInterface $session,
        LoggerInterface $logger,
        UserService $userService
    ) {
        $this->responder = $responder;
        $this->twig = $twig;
        $this->session = $session;
        $this->logger = $logger;
        $this->userService = $userService;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request
     * @param array $args
     *
     * @return ResponseInterface
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
