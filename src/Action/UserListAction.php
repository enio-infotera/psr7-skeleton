<?php

namespace App\Action;

use App\Domain\User\UserList;
use App\Http\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class UserListAction implements ActionInterface
{
    /**
     * @var JsonResponder
     */
    private $responder;

    /**
     * @var UserList
     */
    private $service;

    /**
     * Constructor.
     *
     * @param JsonResponder $responder The responder
     * @param UserList $service The user list service
     */
    public function __construct(JsonResponder $responder, UserList $service)
    {
        $this->responder = $responder;
        $this->service = $service;
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
        $params = (array)$request->getParsedBody();
        $result = $this->service->listAllUsers($params);

        return $this->responder->encode($result);
    }
}
