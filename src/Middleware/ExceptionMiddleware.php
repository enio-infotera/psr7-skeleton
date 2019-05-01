<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Error handling middleware.
 *
 * Traps exceptions and converts them into a error page.
 */
final class ExceptionMiddleware implements MiddlewareInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    /** @var LoggerInterface|null */
    private $logger;

    /** @var bool */
    private $verbose;

    /**
     * Set the Middleware instance.
     *
     * @param ResponseFactoryInterface $responseFactory The repository factory
     * @param StreamFactoryInterface $streamFactory The stream factory
     * @param bool $verbose Verbose error output Verbose logging or not
     * @param LoggerInterface|null $logger The logger
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        bool $verbose = false,
        ?LoggerInterface $logger = null
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->logger = $logger;
        $this->verbose = $verbose;
    }

    /**
     * Wrap the remaining middleware with error handling.
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface The response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Handle an exception and generate an error response.
     *
     * @param Throwable $exception the exception to handle
     *
     * @return ResponseInterface the response
     */
    public function handleException(Throwable $exception): ResponseInterface
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
