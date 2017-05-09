<?php

// Add routes: httpMethod, route, handler
$routes = [];

// Default page
$routes[] = ['GET', '/', function ($request, $response) {
    $ctrl = new App\Controller\IndexController($request, $response);
    return $ctrl->index();
}];

// Controller action
// Object method call with Class->method
$routes[] = ['GET', '/users',function ($request, $response) {
    $ctrl = new App\Controller\UserController($request, $response);
    return $ctrl->indexPage();
}];


// {id} must be a number (\d+)
$routes[] = ['GET', '/users/{id:\d+}', function ($request, $response) {
    $ctrl = new App\Controller\UserController($request, $response);
    return $ctrl->editPage();
}];

// Sub-Resource
$routes[] = ['GET', '/users/{id:\d+}/reviews', function ($request, $response) {
    $ctrl = new App\Controller\UserController($request, $response);
    return $ctrl->reviewPage();
}];

return $routes;
