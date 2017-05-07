<?php

// Add routes: httpMethod, route, handler
$routes = [];

// Default page
$routes[] = ['GET', '/', function ($ctx) {
    $ctrl = new App\Controller\IndexController();
    return $ctrl->index($ctx);
}];

// Controller action
// Object method call with Class->method
$routes[] = ['GET', '/users',function ($ctx) {
    $ctrl = new App\Controller\UserController();
    return $ctrl->index($ctx);
}];

// Static class method call with Class::method
$routes[] = ['GET', '/user/test', function ($ctx) {
    $ctrl = new App\Controller\UserController();
    return $ctrl->test($ctx);
}];

// {id} must be a number (\d+)
$routes[] = ['GET', '/user/{id:\d+}', function ($ctx) {
    $ctrl = new App\Controller\UserController();
    return $ctrl->edit($ctx);
}];

return $routes;
