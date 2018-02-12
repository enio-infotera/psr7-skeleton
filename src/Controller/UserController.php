<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;

/**
 * UserController
 */
class UserController extends AppController
{

    /**
     * Index page.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function indexPage(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // Append content to response
        $response->getBody()->write("User index action<br>");
        return $response;
    }

    /**
     * Edit page.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function editPage(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $id = $request->getAttribute('id');
        $response->getBody()->write("Edit user with ID: $id<br>");
        return $response;
    }

    /**
     * Test page.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function reviewPage(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        throw new \RuntimeException('xxx');

        $response = $handler->handle($request);

        $id = $request->getAttribute('id');
        $response->getBody()->write("Action: Show all reviews of User: $id<br>");

        /// Uncomment this line to test the ExceptionMiddleware
        //throw new \Exception('My error', 1234);

        return $response;
    }
}
