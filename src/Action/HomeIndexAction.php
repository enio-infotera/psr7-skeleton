<?php

namespace App\Action;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action
 */
final class HomeIndexAction implements ActionInterface
{

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Index page.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $body = $response->getBody();

        $body->write('Hello world<br>Default index page<br><br>');
        $body->write('Users: <a href="users">users/</a><br>');
        $body->write('User 1234: <a href="users/1234">users/1234</a><br>');
        $body->write('User 1234 reviews: <a href="users/1234/reviews">users/1234/reviews</a><br>');

        return $response;
    }
}
