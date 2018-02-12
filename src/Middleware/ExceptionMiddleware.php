<?php

namespace App\Middleware;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

/**
 * Error handling middleware.
 *
 * Traps exceptions and converts them into a error page.
 */
class ExceptionMiddleware implements MiddlewareInterface
{

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
    public function __construct(array $options = array())
    {
        $default = [
            'verbose' => false,
            'logger' => 1,
        ];
        $this->options = $options + $default;
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
            return $this->handleException($e, $request, new Response());
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
    public function handleException(Exception $ex, Request $request, Response $response)
    {
        $message = sprintf("[%s] %s\n%s", get_class($ex), $ex->getMessage(), $ex->getTraceAsString());

        // Must be PSR logger (Monolog)
        if (!empty($this->options['logger'])) {
            $this->options['logger']->error($message);
        }
        $stream = new Stream('php://temp', 'wb+');
        $stream->write('An Internal Server Error Occurred');

        // Verbose error output
        if (!empty($this->options['verbose'])) {
            $stream->write("\n<br>$message");
        }

        return $response->withStatus(500)->withBody($stream);
    }
}
