<?php

namespace App\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

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
     * @throws RuntimeException
     *
     * @return ResponseInterface
     */
    public function encode($data = null): ResponseInterface
    {
        $json = json_encode($data);
        if ($json === false) {
            throw new RuntimeException('Encoding to JSON failed');
        }

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json;charset=utf-8');

        $response->getBody()->write($json);

        return $response;
    }
}
