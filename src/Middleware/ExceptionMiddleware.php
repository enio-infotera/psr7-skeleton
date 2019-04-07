<?php

namespace App\Middleware;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Error handling middleware.
 *
 * Traps exceptions and converts them into a error page.
 */
class ExceptionMiddleware implements MiddlewareInterface
{

    private $responseFactory;
    private $streamFactory;

    /**
     * Options
     *
     * @var array
     */
    protected $options = array();

    /**
     * Set the Middleware instance.
     *
     * @param array $options
     */
    public function __construct(array $options = array(), ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory)
    {
        $default = [
            'verbose' => false,
            'logger' => 1,
        ];
        $this->options = $options + $default;

        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Wrap the remaining middleware with error handling.
     *
     * @param ServerRequestInterface $request The request.
     * @param RequestHandlerInterface $handler The next handler.
     * @return Response A response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Exception $e) {
            return $this->handleException($e, $request);
        }
    }

    /**
     * Handle an exception and generate an error response.
     *
     * @param Exception $ex The exception to handle.
     * @param Request $request The request.
     * @param Response $response The response.
     * @return Response A response
     */
    public function handleException(Exception $ex, RequestInterface $request)
    {
        $message = sprintf("[%s] %s\n%s", get_class($ex), $ex->getMessage(), $ex->getTraceAsString());

        // Must be PSR logger (Monolog)
        if (!empty($this->options['logger'])) {
            $this->options['logger']->error($message);
        }
        $stream = $this->streamFactory->createStream();
        $stream->write('An Internal Server Error Occurred');

        // Verbose error output
        if (!empty($this->options['verbose'])) {
            $stream->write("\n<br>$message");
        }

        return $this->responseFactory->createResponse()->withStatus(500)->withBody($stream);
    }
}
