<?php

/**
 * Simple as possible PSR-7 application
 *
 * @license MIT
 * @author odan
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

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

(new SapiEmitter())->emit(call_user_func(function () {
    // Bootstrap
    $container = require __DIR__ . '/../config/container.php';

    require __DIR__ . '/../config/middleware.php';

    $router = require __DIR__ . '/../config/routes.php';

    // Dispatch
    return $router->dispatch($container->get(ServerRequestInterface::class));
}));
