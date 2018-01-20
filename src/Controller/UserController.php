<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * UserController
 */
class UserController extends AppController
{

    /**
     * Index page.
     *
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function indexPage(Request $request, Response $response): ResponseInterface
    {
        // Append content to response
        $response->getBody()->write("User index action<br>");
        return $response;
    }

    /**
     * Edit page.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function editPage(Request $request, Response $response): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $response->getBody()->write("Edit user with ID: $id<br>");
        return $response;
    }

    /**
     * Test page.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function reviewPage(Request $request, Response $response): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $response->getBody()->write("Action: Show all reviews of User: $id<br>");

        /// Uncomment this line to test the ExceptionMiddleware
        //throw new \Exception('My error', 1234);

        return $response;
    }
}
