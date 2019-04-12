<?php

use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = require __DIR__ . '/bootstrap.php';

$pdo = $container->get(PDO::class);

$phinx = $container->get('settings')['phinx'];

$phinx['environments']['local'] = [
    // Set database name
    'name' => $pdo->query('select database()')->fetchColumn(),
    'connection' => $pdo,
];

return $phinx;
