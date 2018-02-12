<?php

// Add routes: httpMethod, route, handler
$routes = [];

// Default page
$routes[] = ['GET', '/', function ($request, $handler) {
    $ctrl = new App\Controller\IndexController();
    return $ctrl->index($request, $handler);
}];

// Controller action
// Object method call with Class->method
$routes[] = ['GET', '/users', function ($request, $handler) {
    $ctrl = new App\Controller\UserController();
    return $ctrl->indexPage($request, $handler);
}];


// {id} must be a number (\d+)
$routes[] = ['GET', '/users/{id:\d+}', function ($request, $handler) {
    $ctrl = new App\Controller\UserController();
    return $ctrl->editPage($request, $handler);
}];

// Sub-Resource
$routes[] = ['GET', '/users/{id:\d+}/reviews', function ($request, $handler) {
    $ctrl = new App\Controller\UserController();
    return $ctrl->reviewPage($request, $handler);
}];

return $routes;
