<?php

use App\Middleware\ExceptionMiddleware;
use App\Middleware\NotFoundMiddleware;
use League\Container\Container;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;

$container = new Container();

$container->share(ExceptionMiddleware::class, function (Container $container) {
    return new ExceptionMiddleware(
        $container->get(ResponseFactoryInterface::class),
        $container->get(StreamFactoryInterface::class),
        true // verbose
    );
})->addArgument($container);

$container->share(NotFoundMiddleware::class, function (Container $container) {
    return new NotFoundMiddleware($container->get(ResponseFactoryInterface::class));
})->addArgument($container);

$container->share(Router::class, function (Container $container) {
    $router = new Router();

    $router->setStrategy((new class() extends ApplicationStrategy
    {
        public function getNotFoundDecorator(NotFoundException $exception): MiddlewareInterface
        {
            return $this->getContainer()->get(NotFoundMiddleware::class);
        }
    })->setContainer($container));

    return $router;
})->addArgument($container);

$container->share(Psr17Factory::class, function () {
    return new Psr17Factory();
});

$container->share(ServerRequestCreator::class, function (Container $container) {
    $psr17Factory = $container->get(Psr17Factory::class);
    return new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
})->addArgument($container);

$container->share(ServerRequestInterface::class, function (Container $container) {
    $creator = $container->get(ServerRequestCreator::class);
    return $creator->fromGlobals();
})->addArgument($container);

$container->share(ResponseFactoryInterface::class, function (Container $container) {
    return $container->get(Psr17Factory::class);
})->addArgument($container);

$container->share(StreamFactoryInterface::class, function (Container $container) {
    return $container->get(Psr17Factory::class);
})->addArgument($container);

$container->share(ResponseInterface::class, function (Container $container) {
    return $container->get(Psr17Factory::class)->createResponse('200');
})->addArgument($container);

//
// Actions
//
$container->add(\App\Action\HomeIndexAction::class, function (Container $container) {
    $responseFactory = $container->get(ResponseFactoryInterface::class);
    return new \App\Action\HomeIndexAction($responseFactory);
})->addArgument($container);

$container->add(\App\Action\UserIndexAction::class, function (Container $container) {
    $responseFactory = $container->get(ResponseFactoryInterface::class);
    return new \App\Action\UserIndexAction($responseFactory);
})->addArgument($container);

$container->add(\App\Action\UserEditAction::class, function (Container $container) {
    $responseFactory = $container->get(ResponseFactoryInterface::class);
    return new \App\Action\UserEditAction($responseFactory);
})->addArgument($container);

$container->add(\App\Action\UserReviewAction::class, function (Container $container) {
    $responseFactory = $container->get(ResponseFactoryInterface::class);
    return new \App\Action\UserReviewAction($responseFactory);
})->addArgument($container);

return $container;