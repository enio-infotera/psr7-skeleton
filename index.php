<?php

/**
 * Simple as possible PSR-7 application
 *
 * @license MIT
 * @author odan
 */
require_once __DIR__ . '/vendor/autoload.php';

use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

$dispatcher = new \Middlewares\Utils\Dispatcher(require __DIR__ . '/config/middleware.php');
$response = $dispatcher->dispatch(ServerRequestFactory::fromGlobals());

// Output response
$emitter = new SapiEmitter();
$emitter->emit($response);
