<?php

namespace App\Controller;

use Zend\Diactoros\Response;

/**
 * IndexController
 */
class IndexController extends AppController
{

    /**
     * Index page.
     *
     * @return Response
     */
    public function index()
    {
        $response = $this->getResponse();
        $body = $response->getBody();

        $body->write("Hello world<br>Default index page<br><br>");
        $body->write('Users: <a href="users">users/</a><br>');
        $body->write('User 1234: <a href="users/1234">users/1234</a><br>');
        $body->write('User 1234 reviews: <a href="users/1234/reviews">users/1234/reviews</a><br>');

        return $response;
    }
}
