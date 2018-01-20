<?php

// Add routes: httpMethod, route, handler
$routes = [];

// Default page
$routes[] = ['GET', '/', function ($request, $response) {
    $ctrl = new App\Controller\IndexController();
    return $ctrl->index($request, $response);
}];

// Controller action
// Object method call with Class->method
$routes[] = ['GET', '/users', function ($request, $response) {
    $ctrl = new App\Controller\UserController();
    return $ctrl->indexPage($request, $response);
}];


// {id} must be a number (\d+)
$routes[] = ['GET', '/users/{id:\d+}', function ($request, $response) {
    $ctrl = new App\Controller\UserController();
    return $ctrl->editPage($request, $response);
}];

// Sub-Resource
$routes[] = ['GET', '/users/{id:\d+}/reviews', function ($request, $response) {
    $ctrl = new App\Controller\UserController();
    return $ctrl->reviewPage($request, $response);
}];

return $routes;
