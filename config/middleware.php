<?php

use League\Route\Router;

$router = $container->get(Router::class);

$router->middleware($container->get(\App\Middleware\ExceptionMiddleware::class));

return $router;
