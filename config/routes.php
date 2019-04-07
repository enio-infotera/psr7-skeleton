<?php

// Add routes: httpMethod, route, handler
$routes = [];

// Default page
$routes[] = ['GET', '/', App\Action\HomeIndexAction::class];

// Users
$routes[] = ['GET', '/users', App\Action\UserIndexAction::class];

// {id} must be a number (\d+)
$routes[] = ['GET', '/users/{id:\d+}', App\Action\UserEditAction::class];

// Sub-Resource
$routes[] = ['GET', '/users/{id:\d+}/reviews', App\Action\UserReviewAction::class];

return $routes;
