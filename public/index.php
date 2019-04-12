<?php

use League\Route\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

require_once __DIR__ . '/../vendor/autoload.php';

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../config/bootstrap.php';

// Dispatch
$response = $container->get(Router::class)->dispatch($container->get(ServerRequestInterface::class));
(new SapiEmitter())->emit($response);
