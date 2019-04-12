<?php

namespace App\Action;

use App\Domain\User\UserList;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
class UserListAction implements ActionInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var UserList
     */
    protected $service;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param UserList $service
     */
    public function __construct(ResponseFactoryInterface $responseFactory, UserList $service)
    {
        $this->responseFactory = $responseFactory;
        $this->service = $service;
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
        $params = (array)$request->getParsedBody();
        $result = $this->service->listAllUsers($params);

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(json_encode($result) ?: '');

        return $response;
    }
}
