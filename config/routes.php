<?php

use League\Route\RouteGroup;
use League\Route\Router;

$router = $container->get(Router::class);

// Default page
$router->get('/', App\Action\HomeIndexAction::class);

// Users
$router->get('/users', App\Action\UserIndexAction::class);

// {id} must be a number (\d+)
$router->get('/users/{id:\d+}', App\Action\UserEditAction::class);

// Sub-Resource
$router->get('/users/{id:\d+}/reviews', App\Action\UserReviewAction::class);

// Routing group
$router->group('/admin', function (RouteGroup $group) {
    $group->get('', 'handler');
    $group->get('/do-something', 'handler');
    $group->get('/do-another-thing', 'handler');
    $group->get('/do-something-else', 'handler');
});

return $router;
