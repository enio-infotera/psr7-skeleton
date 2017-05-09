<?php

namespace App\Controller;

use Zend\Diactoros\Response;

/**
 * UserController
 */
class UserController extends AppController
{

    /**
     * Index page.
     *
     * @return Response
     */
    public function indexPage()
    {
        // Append content to response
        $response = $this->getResponse();
        $response->getBody()->write("User index action<br>");
        return $response;
    }

    /**
     * Edit page.
     *
     * @return Response
     */
    public function editPage()
    {
        $request = $this->getRequest();
        $id = $request->getAttribute('id');

        $response = $this->getResponse();
        $response->getBody()->write("Edit user with ID: $id<br>");
        return $response;
    }

    /**
     * Test page.
     *
     * @return Response
     */
    public function reviewPage()
    {
        $request = $this->getRequest();
        $id = $request->getAttribute('id');

        $response = $this->getResponse();
        $response->getBody()->write("Action: Show all reviews of User: $id<br>");

        /// Uncomment this line to test the ExceptionMiddleware
        //throw new \Exception('My error', 1234);

        return $response;
    }
}
