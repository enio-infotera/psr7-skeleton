<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * IndexController
 */
class IndexController extends AppController
{

    /**
     * Index page.
     *
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function index(Request $request, Response $response): ResponseInterface
    {
        $body = $response->getBody();

        $body->write("Hello world<br>Default index page<br><br>");
        $body->write('Users: <a href="users">users/</a><br>');
        $body->write('User 1234: <a href="users/1234">users/1234</a><br>');
        $body->write('User 1234 reviews: <a href="users/1234/reviews">users/1234/reviews</a><br>');

        return $response;
    }
}
