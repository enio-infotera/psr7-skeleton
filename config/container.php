<?php

use App\Utility\Psr11Container;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

$container = new Psr11Container();

$container[Psr17Factory::class] = function (ContainerInterface $container) {
    return new Psr17Factory();
};

$container[ServerRequestCreator::class] = function (ContainerInterface $container) {
    $psr17Factory = $container->get(Psr17Factory::class);
    return new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
};

$container[ServerRequestInterface::class] = function (ContainerInterface $container) {
    $creator = $container->get(ServerRequestCreator::class);
    return $creator->fromGlobals();
};

$container[ResponseFactoryInterface::class] = function (ContainerInterface $container) {
    return $container->get(Psr17Factory::class);
};

$container[StreamFactoryInterface::class] = function (ContainerInterface $container) {
    return $container->get(Psr17Factory::class);
};

$container[ResponseInterface::class] = function (ContainerInterface $container) {
    return $container->get(Psr17Factory::class)->createResponse('200');
};

//
// Actions
//
$container[\App\Action\HomeIndexAction::class] = function (ContainerInterface $container) {
    $responseFactory = $container->get(ResponseFactoryInterface::class);
    return new \App\Action\HomeIndexAction($responseFactory);
};

$container[\App\Action\UserIndexAction::class] = function (ContainerInterface $container) {
    $responseFactory = $container->get(ResponseFactoryInterface::class);
    return new \App\Action\UserIndexAction($responseFactory);
};

$container[\App\Action\UserEditAction::class] = function (ContainerInterface $container) {
    $responseFactory = $container->get(ResponseFactoryInterface::class);
    return new \App\Action\UserEditAction($responseFactory);
};

$container[\App\Action\UserReviewAction::class] = function (ContainerInterface $container) {
    $responseFactory = $container->get(ResponseFactoryInterface::class);
    return new \App\Action\UserReviewAction($responseFactory);
};

return $container;