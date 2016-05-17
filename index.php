<?php

/**
 * Simple as possible PSR-7 application
 *
 * Hello world
 *
 * @license MIT
 * @author odan
 */
require_once __DIR__ . '/vendor/autoload.php';

use Relay\Runner;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\ServerRequestFactory;

//use Zend\Diactoros\Response\RedirectResponse;
//use Zend\Diactoros\Response\HtmlResponse;
//use Zend\Diactoros\Response\JsonResponse;

//
// Create a queue array of middleware callables
//
$queue = [];

$queue[] = \App\Middleware\ExceptionMiddleware::class;

$queue[] = function (Request $request, Response $response, callable $next) {
    // Hello world middleware
    // Append content to response
    $response->getBody()->write('Hello world');

    // Uncomment this line to test the ExceptionMiddleware
    //throw new Exception('My error', 1234);
    //
    // Invoke the $next middleware and get back a new response
    $response = $next($request, $response);
    return $response;
};

//
// Invoke the relay queue with a request and response.
//
$runner = new Runner($queue, function($class) {
    if (is_string($class)) {
        return new $class();
    } else {
        return $class;
    }
});
$response = $runner(ServerRequestFactory::fromGlobals(), new Response());

//
// Output response
//
$emitter = new SapiEmitter();
$emitter->emit($response);
