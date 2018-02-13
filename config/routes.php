<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

// Add routes: httpMethod, route, handler
$routes = [];

// Default page
$routes[] = ['GET', '/', function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
    $action = new App\Action\HomeIndexAction();
    return $action->process($request, $handler);
}];

// Controller action
// Object method call with Class->method
$routes[] = ['GET', '/users', function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
    $action = new App\Action\UserIndexAction();
    return $action->process($request, $handler);
}];

// {id} must be a number (\d+)
$routes[] = ['GET', '/users/{id:\d+}', function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
    $action = new App\Action\UserEditAction();
    return $action->process($request, $handler);
}];

// Sub-Resource
$routes[] = ['GET', '/users/{id:\d+}/reviews', function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
    $action = new App\Action\UserReviewAction();
    return $action->process($request, $handler);
}];

return $routes;
