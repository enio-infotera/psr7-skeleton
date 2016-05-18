<?php

/**
 * Simple as possible PSR-7 application
 *
 * @license MIT
 * @author odan
 */
require_once __DIR__ . '/vendor/autoload.php';

use Relay\Runner;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

//
// Create a queue array of middleware callables
//
$queue = [];

// Error handler
$queue[] = new \App\Middleware\ExceptionMiddleware(['verbose' => true, 'logger' => null]);

// Router
$routes = require_once __DIR__ . '/src/Config/routes.php';
$queue[] = new \App\Middleware\FastRouteMiddleware(['routes' => $routes]);

//
// Invoke the relay queue with a request and response.
//
$runner = new Runner($queue);
$response = $runner(ServerRequestFactory::fromGlobals(), new Response());

//
// Output response
//
$emitter = new SapiEmitter();
$emitter->emit($response);
