<?php

use App\Domain\User\Auth;
use App\Domain\User\AuthRepository;
use App\Domain\User\Locale;
use App\Factory\LoggerFactory;
use App\Http\RouterUrl;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\ExceptionMiddleware;
use App\Middleware\LanguageMiddleware;
use App\Middleware\NotFoundMiddleware;
use App\Middleware\SessionMiddleware;
use App\Repository\QueryFactory;
use Cake\Chronos\Chronos;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Odan\Csrf\CsrfMiddleware;
use Odan\Session\MemorySession;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Odan\Twig\TwigAssetsExtension;
use Odan\Twig\TwigTranslationExtension;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment as Twig;
use Twig\Loader\FilesystemLoader;

$container = new Container();

$container->delegate(new ReflectionContainer());

// Core
$container->share(ContainerInterface::class, static function (Container $container) {
    return $container;
})->addArgument($container);

$container->share(Router::class, function (Container $container) {
    $router = new Router();

    $router->setStrategy((new class() extends ApplicationStrategy {
        public function getNotFoundDecorator(NotFoundException $exception): MiddlewareInterface
        {
            return $this->getContainer()->get(NotFoundMiddleware::class);
        }
    })->setContainer($container));

    return $router;
})->addArgument($container);

$container->share(NotFoundMiddleware::class, static function (Container $container) {
    return new NotFoundMiddleware($container->get(ResponseFactoryInterface::class));
})->addArgument($container);

$container->share(SessionMiddleware::class, static function (Container $container) {
    return new SessionMiddleware($container->get(SessionInterface::class));
})->addArgument($container);

$container->share(CsrfMiddleware::class, static function (Container $container) {
    $psr17Factory = $container->get(Psr17Factory::class);
    $session = $container->get(SessionInterface::class);

    // Workaround, until thephpleague/route offers support for lazy loading middleware #218
    if (!$session->isStarted()) {
        $session->start();
    }

    return new CsrfMiddleware($psr17Factory, $session->getId());
})->addArgument($container);

$container->share(Psr17Factory::class, static function () {
    return new Psr17Factory();
});

$container->share(ServerRequestCreator::class, static function (Container $container) {
    $psr17Factory = $container->get(Psr17Factory::class);

    return new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
})->addArgument($container);

$container->share(ServerRequestInterface::class, static function (Container $container) {
    $creator = $container->get(ServerRequestCreator::class);

    if (PHP_SAPI === 'cli') {
        return $creator->fromArrays([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_SCHEME' => 'http',
            'HTTP_HOST' => 'localhost',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'SCRIPT_NAME' => '',
        ]);
    }

    // Change the request uri to run the app in a subdirectory.
    $_SERVER['REQUEST_URI_ORG'] = $_SERVER['REQUEST_URI'];
    $path = parse_url($_SERVER['REQUEST_URI'])['path'];
    $scriptName = dirname(dirname($_SERVER['SCRIPT_NAME']));
    $len = strlen($scriptName);
    if ($len > 0 && $scriptName !== '/') {
        $path = substr($path, $len);
    }
    $_SERVER['REQUEST_URI'] = $path ?: '';

    return $creator->fromGlobals();
})->addArgument($container);

$container->share(RouterUrl::class, static function (Container $container) {
    $routeUrl = new RouterUrl($container->get(Router::class));

    // Detect base path
    $serverParams = $container->get(ServerRequestInterface::class)->getServerParams();
    $basePath = dirname(dirname($serverParams['SCRIPT_NAME']));

    if ($basePath !== '' && $basePath !== '/') {
        $routeUrl->setBasePath($basePath);
    }

    return $routeUrl;
})->addArgument($container);

$container->share(ResponseFactoryInterface::class, static function (Container $container) {
    return $container->get(Psr17Factory::class);
})->addArgument($container);

$container->share(StreamFactoryInterface::class, static function (Container $container) {
    return $container->get(Psr17Factory::class);
})->addArgument($container);

$container->share(ResponseInterface::class, static function (Container $container) {
    return $container->get(Psr17Factory::class)->createResponse('200');
})->addArgument($container);

// Custom definitions
$container->share('settings', static function () {
    return require __DIR__ . '/settings.php';
});

$container->share(LoggerInterface::class, static function (Container $container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Logger($settings['name']);

    $level = isset($settings['level']) ?: Logger::ERROR;
    $filename = sprintf('%s/%s', $settings['path'], $settings['filename']);

    $handler = new RotatingFileHandler($filename, 0, $level, true, 0775);
    $logger->pushHandler($handler);

    return $logger;
})->addArgument($container);

$container->share(LoggerFactory::class, static function (Container $container) {
    return new LoggerFactory($container->get('settings')['logger']);
})->addArgument($container);

$container->share(Locale::class, static function (Container $container) {
    $translator = $container->get(Translator::class);
    $session = $container->get(SessionInterface::class);
    $localPath = $container->get('settings')['locale']['path'];

    return new Locale($translator, $session, $localPath);
})->addArgument($container);

$container->share(SessionInterface::class, static function (Container $container) {
    $settings = $container->get('settings');
    $session = PHP_SAPI === 'cli' ? new MemorySession() : new PhpSession();
    $session->setOptions((array)$settings['session']);

    return $session;
})->addArgument($container);

$container->share(Auth::class, static function (Container $container) {
    return new Auth($container->get(SessionInterface::class), $container->get(AuthRepository::class));
})->addArgument($container);

$container->share(AuthRepository::class, static function (Container $container) {
    return new AuthRepository($container->get(QueryFactory::class));
})->addArgument($container);

$container->share(Connection::class, static function (Container $container) {
    $settings = $container->get('settings');
    $driver = new Mysql($settings['db']);

    return new Connection(['driver' => $driver]);
})->addArgument($container);

$container->share(PDO::class, static function (Container $container) {
    $db = $container->get(Connection::class);
    $db->getDriver()->connect();

    return $db->getDriver()->getConnection();
})->addArgument($container);

$container->share(QueryFactory::class, static function (Container $container) {
    $queryFactory = new QueryFactory($container->get(Connection::class));

    $queryFactory->beforeUpdate(static function (array $row) use ($container) {
        if (!isset($row['updated_at'])) {
            $row['updated_at'] = Chronos::now()->toDateTimeString();
        }

        if (!isset($row['updated_user_id'])) {
            $row['updated_user_id'] = $container->get(Auth::class)->getUserId();
        }

        return $row;
    });

    $queryFactory->beforeInsert(static function (array $row) use ($container) {
        if (!isset($row['created_at'])) {
            $row['created_at'] = Chronos::now()->toDateTimeString();
        }

        if (!isset($row['created_user_id'])) {
            $row['created_user_id'] = $container->get(Auth::class)->getUserId();
        }

        return $row;
    });

    return $queryFactory;
})->addArgument($container);

$container->share(Twig::class, static function (Container $container) {
    $settings = $container->get('settings');
    $viewPath = $settings['twig']['path'];

    $loader = new FilesystemLoader($viewPath);

    $twig = new Twig($loader, [
        'cache' => $settings['twig']['cache_enabled'] ? $settings['twig']['cache_path'] : false,
    ]);

    if ($loader instanceof FilesystemLoader) {
        $loader->addPath($settings['public'], 'public');
    }

    // Add CSRF token as global template variable
    $csrfToken = $container->get(CsrfMiddleware::class)->getToken();
    $twig->addGlobal('csrf_token', $csrfToken);

    $twig->addGlobal('base_url', $container->get(RouterUrl::class)->pathFor('root'));
    $twig->addGlobal('globalText', $container->get('globalText'));

    // Add Twig extensions
    $twig->addExtension(new TwigAssetsExtension($twig, (array)$settings['assets']));
    $twig->addExtension(new TwigTranslationExtension());

    return $twig;
})->addArgument($container);

$container->share('globalText', static function () {
    return [
        'Ok' => __('Ok'),
        'Cancel' => __('Cancel'),
        'Yes' => __('Yes'),
        'No' => __('No'),
        'Edit' => __('Edit'),
        'js/datatable-english.json' => __('js/datatable-english.json'),
    ];
});

$container->share(Translator::class, static function (Container $container) {
    $settings = $container->get('settings')['locale'];
    $translator = new Translator(
        $settings['locale'],
        new MessageFormatter(
            new IdentityTranslator()
        ),
        $settings['cache']
    );
    $translator->addLoader('mo', new MoFileLoader());

    return $translator;
})->addArgument($container);

// Middleware
$container->share(ExceptionMiddleware::class, static function (Container $container) {
    return new ExceptionMiddleware(
        $container->get(ResponseFactoryInterface::class),
        $container->get(StreamFactoryInterface::class),
        true // verbose
    );
})->addArgument($container);

$container->share(CorsMiddleware::class, static function (Container $container) {
    return new CorsMiddleware();
})->addArgument($container);

$container->share(LanguageMiddleware::class, static function (Container $container) {
    return new LanguageMiddleware($container->get(Locale::class));
})->addArgument($container);

$container->share(AuthenticationMiddleware::class, static function (Container $container) {
    return new AuthenticationMiddleware(
        $container->get(ResponseFactoryInterface::class),
        $container->get(RouterUrl::class),
        $container->get(Auth::class)
    );
})->addArgument($container);

return $container;
