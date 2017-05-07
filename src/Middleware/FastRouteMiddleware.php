<?php

namespace App\Middleware;

use App\Http\ActionContext;
use Exception;
use RuntimeException;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

/**
 * FastRouteMiddleware
 */
class FastRouteMiddleware
{

    /**
     * Options
     *
     * @var array
     */
    protected $options = array();

    /**
     * @var Dispatcher FastRoute dispatcher
     */
    private $router;

    /**
     * Set the Dispatcher instance.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $default = [
            'routes' => [],
            'dispatcher' => 'FastRoute\simpleDispatcher',
            'arguments' => []
        ];
        $this->options = $options + $default;

        $dispatcher = $this->options['dispatcher'];
        $routes = $this->options['routes'];
        $this->router = $dispatcher(function (RouteCollector $r) use ($routes) {
            foreach ($routes as $route) {
                // $httpMethod, $route, $handler
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        });
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
        $uri = $this->getBaseUri($request);
        $route = $this->router->dispatch($request->getMethod(), $uri);
        if ($route[0] === Dispatcher::NOT_FOUND) {
            $stream = new Stream('php://temp', 'wb+');
            $stream->write('Not found');
            return $response->withStatus(404)->withBody($stream);
        }
        if ($route[0] === Dispatcher::METHOD_NOT_ALLOWED) {
            $stream = new Stream('php://temp', 'wb+');
            $stream->write('Not allowed');
            return $response->withStatus(405)->withBody($stream);
        }
        foreach ($route[2] as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }
        $response = $this->executeCallable($route[1], $request, $response);
        return $next($request, $response);
    }

    /**
     * Returns the url path leading up to the current script.
     * Used to make the web app portable to other locations.
     *
     * @param Request $request
     * @return string uri
     */
    public function getBaseUri(Request $request)
    {
        // Get URI from URL
        $uri = $request->getUri()->getPath();

        // Detect and remove subfolder from URI
        $server = $request->getServerParams();
        $scriptName = $server['SCRIPT_NAME'];

        if (isset($scriptName)) {
            $path = dirname($scriptName);
            $len = strlen($path);
            if ($len > 0 && $path != '/') {
                $uri = substr($uri, $len);
            }
        }
        return $uri;
    }

    /**
     * Execute the callable.
     *
     * @param callable $target
     * @param Request $request
     * @param Response $response
     * @throws Exception On error
     *
     * @return Response
     */
    private function executeCallable(callable $target, Request $request, Response $response)
    {
        ob_start();
        $level = ob_get_level();
        try {
            $ctx = new ActionContext($request, $response);
            $arguments = $this->options['arguments'];
            $return = call_user_func_array($target, [$ctx, $arguments]);
            if ($return instanceof Response) {
                $response = $return;
                $return = '';
            }
            $return = $this->getOutput($level) . $return;
            $body = $response->getBody();
            if ($return !== '' && $body->isWritable()) {
                $body->write($return);
            }
            return $response;
        } catch (Exception $exception) {
            $this->getOutput($level);
            throw $exception;
        }
    }

    /**
     * Return the output buffer.
     *
     * @param int $level
     *
     * @return string
     */
    public static function getOutput($level)
    {
        $output = '';
        while (ob_get_level() >= $level) {
            $output .= ob_get_clean();
        }
        return $output;
    }
}
