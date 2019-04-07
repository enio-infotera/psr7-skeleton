<?php

use App\Middleware\ExceptionMiddleware;
use League\Container\Container;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

$container = new Container();

$container->share(ExceptionMiddleware::class, function () use ($container) {
    return new ExceptionMiddleware(
        $container->get(ResponseFactoryInterface::class),
        $container->get(StreamFactoryInterface::class),
        true // verbose
    );
});

$container->share(Router::class, function () use ($container) {
    $strategy = (new ApplicationStrategy())->setContainer($container);
    $router = new Router();
    $router->setStrategy($strategy);

    return $router;
});

$container->share(Psr17Factory::class, function () {
    return new Psr17Factory();
});

$container->share(ServerRequestCreator::class, function () use ($container) {
    $psr17Factory = $container->get(Psr17Factory::class);
    return new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
});

$container->share(ServerRequestInterface::class, function () use ($container) {
    $creator = $container->get(ServerRequestCreator::class);
    return $creator->fromGlobals();
});

$container->share(ResponseFactoryInterface::class, function () use ($container) {
    return $container->get(Psr17Factory::class);
});

$container->share(StreamFactoryInterface::class, function () use ($container) {
    return $container->get(Psr17Factory::class);
});

$container->share(ResponseInterface::class, function () use ($container) {
    return $container->get(Psr17Factory::class)->createResponse('200');
});

//
// Actions
//
$container->add(\App\Action\HomeIndexAction::class, function () use ($container) {
    $responseFactory = $container->get(ResponseFactoryInterface::class);
    return new \App\Action\HomeIndexAction($responseFactory);
});

$container->add(\App\Action\UserIndexAction::class, function () use ($container) {
    $responseFactory = $container->get(ResponseFactoryInterface::class);
    return new \App\Action\UserIndexAction($responseFactory);
});

$container->add(\App\Action\UserEditAction::class, function () use ($container) {
    $responseFactory = $container->get(ResponseFactoryInterface::class);
    return new \App\Action\UserEditAction($responseFactory);
});

$container->add(\App\Action\UserReviewAction::class, function () use ($container) {
    $responseFactory = $container->get(ResponseFactoryInterface::class);
    return new \App\Action\UserReviewAction($responseFactory);
});

return $container;