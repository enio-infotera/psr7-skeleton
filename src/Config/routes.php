<?php

// Add routes: httpMethod, route, handler
$routes = [];

// Default page
$routes[] = ['GET', '/', 'App\Controller\IndexController->index'];

// Controller action
// Object method call with Class->method
$routes[] = ['GET', '/users', 'App\Controller\UserController->index'];

// Static class method call with Class::method
$routes[] = ['GET', '/user/test', 'App\Controller\UserController::test'];

// {id} must be a number (\d+)
$routes[] = ['GET', '/user/{id:\d+}', 'App\Controller\UserController->edit'];

return $routes;
