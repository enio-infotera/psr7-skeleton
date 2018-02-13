<?php

/**
 * Simple as possible PSR-7 application
 *
 * @license MIT
 * @author odan
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

$dispatcher = new \Middlewares\Utils\Dispatcher(require __DIR__ . '/../config/middleware.php');

// Optional: change the request uri to run the app in a subdirectory.
$_SERVER['REQUEST_URI'] = call_user_func(function () {
    $path = parse_url($_SERVER['REQUEST_URI'])['path'];
    $scriptName = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'])));
    $len = strlen($scriptName);
    if ($len > 0 && $scriptName !== '/') {
        $path = substr($path, $len);
    }
    return $path;
});

$response = $dispatcher->dispatch(ServerRequestFactory::fromGlobals());

// Output response
$emitter = new SapiEmitter();
$emitter->emit($response);
