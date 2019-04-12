<?php

namespace App\Test\TestCase;

use App\Test\Base\BaseTestCase;
use Exception;
use League\Route\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class ApiTestCase.
 */
class ApiTestCase extends BaseTestCase
{
    use AppTestTrait;

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->bootApp();
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->shutdownApp();
    }

    /**
     * Create a server request.
     *
     * @param string $method HTTP method
     * @param string|UriInterface $uri URI
     * @param array $serverParams
     *
     * @return ServerRequestInterface
     */
    protected function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return $this->getContainer()->get(Psr17Factory::class)->createServerRequest($method, $uri, $serverParams);
    }

    /**
     * Add post data.
     *
     * @param ServerRequestInterface $request The request
     * @param mixed[] $data The data
     *
     * @return ServerRequestInterface
     */
    protected function withFormData(ServerRequestInterface $request, array $data): ServerRequestInterface
    {
        if (!empty($data)) {
            $request = $request->withParsedBody($data);
        }

        return $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    /**
     * Add Json data.
     *
     * @param ServerRequestInterface $request The request
     * @param mixed[] $data The data
     *
     * @return ServerRequestInterface
     */
    protected function withJson(ServerRequestInterface $request, array $data): ServerRequestInterface
    {
        $request = $request->withParsedBody($data);
        $request = $request->withHeader('Content-Type', 'application/json');

        return $request;
    }

    /**
     * Make request.
     *
     * @param ServerRequestInterface $request The request
     *
     * @throws Exception
     *
     * @return ResponseInterface
     */
    protected function request(ServerRequestInterface $request): ResponseInterface
    {
        return $this->getContainer()->get(Router::class)->dispatch($request);
    }
}
