<?php

// Create a queue array of middleware callable's
$queue = [];

// Error handler
$queue[] = new \App\Middleware\ExceptionMiddleware(['verbose' => true, 'logger' => null]);

// Router
$queue[] = new \App\Middleware\FastRouteMiddleware(['routes' => require __DIR__ . '/routes.php']);

return $queue;
