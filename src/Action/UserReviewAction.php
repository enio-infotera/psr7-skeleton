<?php

namespace App\Action;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action
 */
final class UserReviewAction implements ActionInterface
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
     * User review page.
     *
     * @param ServerRequestInterface $request
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $id = (int)$args['id'];
        $response->getBody()->write("Action: Show all reviews of User: $id<br>");

        /// Uncomment this line to test the ExceptionMiddleware
        //throw new \Exception('My error', 1234);

        return $response;
    }
}
