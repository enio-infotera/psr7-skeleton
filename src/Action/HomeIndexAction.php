<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * HomeIndexAction
 */
class HomeIndexAction extends AbstractAction
{

    /**
     * Index page.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $body = $response->getBody();

        $body->write("Hello world<br>Default index page<br><br>");
        $body->write('Users: <a href="users">users/</a><br>');
        $body->write('User 1234: <a href="users/1234">users/1234</a><br>');
        $body->write('User 1234 reviews: <a href="users/1234/reviews">users/1234/reviews</a><br>');

        return $response;
    }
}