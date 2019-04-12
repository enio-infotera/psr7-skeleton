<?php

namespace App\Action;

use App\Domain\User\UserService;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
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
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

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
     * @param ResponseFactoryInterface $responseFactory
     * @param Twig $twig
     * @param SessionInterface $session
     * @param LoggerInterface $logger
     * @param UserService $userService
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        Twig $twig,
        SessionInterface $session,
        LoggerInterface $logger,
        UserService $userService
    ) {
        $this->responseFactory = $responseFactory;
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

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($this->twig->render('User/user-edit.twig', $viewData));

        return $response;
    }
}
