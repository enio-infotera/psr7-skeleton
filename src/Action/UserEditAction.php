<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * UserEditAction
 */
class UserEditAction extends AbstractAction
{

    /**
     * User edit page.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $id = $request->getAttribute('id');
        $response->getBody()->write("Edit user with ID: $id<br>");
        return $response;
    }

}
