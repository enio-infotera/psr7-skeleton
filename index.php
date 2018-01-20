<?php

/**
 * Simple as possible PSR-7 application
 *
 * @license MIT
 * @author odan
 */
require_once __DIR__ . '/vendor/autoload.php';

use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

// Invoke the relay queue with a request and response.
$runner = new Relay\Runner(require __DIR__ . '/config/middleware.php');
$response = $runner(ServerRequestFactory::fromGlobals(), new Response());

// Output response
$emitter = new SapiEmitter();
$emitter->emit($response);
