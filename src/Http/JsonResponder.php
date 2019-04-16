<?php

namespace App\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A generic JSON responder.
 */
class JsonResponder
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory the response factory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Generate a json response.
     *
     * @param mixed $data data
     *
     * @return ResponseInterface
     */
    public function encode($data = null): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response = $response->withStatus(200)->withHeader('Content-Type', 'application/json;charset=utf-8');
        $response->getBody()->write(json_encode($data) ?: '');

        return $response;
    }
}
