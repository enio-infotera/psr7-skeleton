<?php

namespace App\Middleware;

use Exception;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

/**
 * Error handling middleware.
 *
 * Traps exceptions and converts them into a error page.
 */
class ExceptionMiddleware
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
     * @param Request $request The request.
     * @param Response $response The response.
     * @param callable $next Callback to invoke the next middleware.
     * @return Response A response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        try {
            return $next($request, $response);
        } catch (Exception $e) {
            return $this->handleException($e, $request, $response);
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

        $response = $response->withStatus(500)->withBody($stream);
        return $response;
    }
}
