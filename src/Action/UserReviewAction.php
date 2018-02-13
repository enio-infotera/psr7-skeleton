<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * UserReviewAction
 */
class UserReviewAction extends AbstractAction
{

    /**
     * User review page.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $id = $request->getAttribute('id');
        $response->getBody()->write("Action: Show all reviews of User: $id<br>");

        /// Uncomment this line to test the ExceptionMiddleware
        //throw new \Exception('My error', 1234);

        return $response;
    }
}
