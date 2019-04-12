<?php

namespace App\Action;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
class HomePingAction implements ActionInterface
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
     * Action.
     *
     * @param ServerRequestInterface $request the request
     *
     * @return ResponseInterface the response
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');
        $response->getBody()->write(json_encode($request->getParsedBody()) ?: '');

        return $response;
    }
}
