<?php

// Create a queue array of middleware callable's
$queue = [];

// Error handler
$queue[] = new \App\Middleware\ExceptionMiddleware(['verbose' => true, 'logger' => null]);

// Router
$routes = require_once __DIR__ . '/routes.php';
$queue[] = new \App\Middleware\FastRouteMiddleware(['routes' => $routes]);

return $queue;
