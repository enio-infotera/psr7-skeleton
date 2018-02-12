<?php

// Create a queue array of middleware callable's
$stack = [];

// Error handler
$stack[] = new \App\Middleware\ExceptionMiddleware(['verbose' => true, 'logger' => null]);

// Router
$stack[] = new Middlewares\FastRoute(FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    foreach (require __DIR__ . '/routes.php' as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
}));

// Must be the last middleware
$stack[] = new Middlewares\RequestHandler();

return $stack;
