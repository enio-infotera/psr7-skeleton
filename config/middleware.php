<?php

use League\Route\Router;

$router = $container->get(Router::class);

//
// Register middleware for all routes
//
$router->middleware($container->get(\App\Middleware\ExceptionMiddleware::class));
$router->middleware($container->get(\App\Middleware\CorsMiddleware::class));

return $router;
