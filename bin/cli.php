<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
    echo "Only CLI allowed. Script stopped.\n";
    exit (1);
}

/** @var \Psr\Container\ContainerInterface $container */
$container = require __DIR__ . '/../config/bootstrap.php';

$commands = $container->get('settings')['commands'];

$application = new \Symfony\Component\Console\Application();

foreach ($commands as $class) {
    if (!class_exists($class)) {
        throw new RuntimeException(sprintf('Class %s does not exist', $class));
    }
    $command = new $class();
    if (method_exists($command, 'setContainer')) {
        $command->setContainer($container);
    }
    $application->add($command);
}

$application->run();