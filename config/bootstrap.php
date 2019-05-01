<?php

use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Translator;

require_once __DIR__ . '/../vendor/autoload.php';

return call_user_func(static function () {
    /** @var ContainerInterface $container */
    $container = require __DIR__ . '/container.php';

    // Register middleware
    require __DIR__ . '/middleware.php';

    // Register routes
    require __DIR__ . '/routes.php';

    // Set translator instance
    __($container->get(Translator::class));

    return $container;
});
