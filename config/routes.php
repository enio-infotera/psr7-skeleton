<?php

//
// Define the routes
//

use App\Middleware\AuthenticationMiddleware;
use App\Middleware\LanguageMiddleware;
use App\Middleware\SessionMiddleware;
use League\Route\RouteGroup;
use League\Route\Router;
use Odan\Csrf\CsrfMiddleware;

$router = $container->get(Router::class);

$router->post('/ping', \App\Action\HomePingAction::class);

// Login, no auth check for this actions required
$router->group('/users', function (RouteGroup $group) {
    $group->post('/login', \App\Action\UserLoginSubmitAction::class);
    $group->get('/login', \App\Action\UserLoginIndexAction::class)->setName('login');
    $group->get('/logout', \App\Action\UserLogoutAction::class);
})
    ->middleware($container->get(SessionMiddleware::class))
    ->middleware($container->get(CsrfMiddleware::class));

// Routes with authentication
$router->group('', function (RouteGroup $group) {
    // Default page
    $group->get('/', \App\Action\HomeIndexAction::class)->setName('root');

    $group->get('/users', \App\Action\UserIndexAction::class);

    $group->post('/users/list', \App\Action\UserListAction::class);

    // This route will only match if {id} is numeric
    $group->get('/users/{id:[0-9]+}', \App\Action\UserEditAction::class)->setName('users.edit');

    // Json request
    $group->post('/home/load', \App\Action\HomeLoadAction::class);
})
    ->middleware($container->get(SessionMiddleware::class))
    ->middleware($container->get(LanguageMiddleware::class))
    ->middleware($container->get(AuthenticationMiddleware::class))
    ->middleware($container->get(CsrfMiddleware::class));

return $router;
