<?php

namespace App\Action;

use App\Domain\User\UserService;
use Twig\Environment as Twig;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class UserIndexAction implements ActionInterface
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
     * @var UserService
     */
    protected $userService;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param Twig $twig
     * @param UserService $userService
     */
    public function __construct(ResponseFactoryInterface $responseFactory, Twig $twig, UserService $userService)
    {
        $this->responseFactory = $responseFactory;
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

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($this->twig->render('User/user-index.twig', $viewData));

        return $response;
    }
}
