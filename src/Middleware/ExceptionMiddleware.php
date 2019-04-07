<?php

namespace App\Middleware;

use Exception;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Error handling middleware.
 *
 * Traps exceptions and converts them into a error page.
 */
final class ExceptionMiddleware implements MiddlewareInterface
{

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $verbose;

    /**
     * Set the Middleware instance.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param StreamFactoryInterface $streamFactory
     * @param LoggerInterface|null $logger
     * @param bool $verbose Verbose error output
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        bool $verbose = false,
        LoggerInterface $logger = null
    )
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->logger = $logger;
        $this->verbose = $verbose;
    }

    /**
     * Wrap the remaining middleware with error handling.
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The next handler
     *
     * @return ResponseInterface The response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Handle an exception and generate an error response.
     *
     * @param Exception $exception The exception to handle.
     *
     * @return ResponseInterface the response
     */
    public function handleException(Exception $exception): ResponseInterface
    {
        $message = sprintf(
            "[%s] %s\n%s",
            get_class($exception),
            $exception->getMessage(),
            $exception->getTraceAsString()
        );

        if (isset($this->logger)) {
            $this->logger->error($message);
        }

        $stream = $this->streamFactory->createStream();
        $stream->write('An Internal Server Error Occurred');

        if ($this->verbose === true) {
            $stream->write(sprintf("\n<br>%s", $message));
        }

        return $this->responseFactory->createResponse()->withStatus(500)->withBody($stream);
    }
}
