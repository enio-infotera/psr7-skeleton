<?php

// Create a stack array of middleware handler
use App\Utility\PimpleContainer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

$stack = [];

// Error handler
$stack[] = new \App\Middleware\ExceptionMiddleware([
    'verbose' => true,
    'logger' => null
],
    $container->get(ResponseFactoryInterface::class),
    $container->get(StreamFactoryInterface::class)
);

// Router
$stack[] = new Middlewares\FastRoute(FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    foreach (require __DIR__ . '/routes.php' as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
}), $container->get(ResponseFactoryInterface::class));

// Must be the last middleware
$stack[] = new Middlewares\RequestHandler($container);

return $stack;
