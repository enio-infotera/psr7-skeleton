<?php

namespace App\Controller;

use App\Http\RequestContext;
use Zend\Diactoros\Response;

/**
 * IndexController
 */
class IndexController
{

    /**
     * Index page.
     *
     * @param RequestContext $ctx Request context
     * @return Response
     */
    public function index(RequestContext $ctx)
    {
        $response = $ctx->getResponse();
        $body = $response->getBody();
        
        $body->write("Hello world<br>Default index page<br><br>");
        $body->write('Users: <a href="users">users/</a><br>');
        $body->write('User 1234: <a href="user/1234">users/1234</a><br>');
        $body->write('Users test: <a href="user/test">users/test</a><br>');

        return $response;
    }
}