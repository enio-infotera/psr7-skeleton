<?php

namespace App\Controller;

use App\Http\ActionContext;
use Zend\Diactoros\Response;

/**
 * UserController
 */
class UserController
{

    /**
     * Index page.
     *
     * @param ActionContext $ctx Request context
     * @return Response
     */
    public function index(ActionContext $ctx)
    {
        // Append content to response
        $response = $ctx->getResponse();
        $response->getBody()->write("User index action<br>");
        return $response;
    }

    /**
     * Edit page.
     *
     * @param ActionContext $ctx Request context
     * @return Response
     */
    public function edit(ActionContext $ctx)
    {
        // Simple echo is also possible.
        // The middleware will catch it and convert it to a response object.
        $request = $ctx->getRequest();
        $id = $request->getAttribute('id');
        echo "Edit user with ID: $id<br>";
        //return $response;
    }

    /**
     * Test page.
     *
     * @param ActionContext $ctx Request context
     * @return Response
     */
    public static function test(ActionContext $ctx)
    {
        $response = $ctx->getResponse();
        $response->getBody()->write("Static test action<br>");

        /// Uncomment this line to test the ExceptionMiddleware
        //throw new \Exception('My error', 1234);
        return $response;
    }
}
