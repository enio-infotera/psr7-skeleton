<?php

namespace App\Test\TestCase;

use League\Container\Container;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Trait.
 */
trait AppTestTrait
{
    /** @var Container|null */
    protected $container;

    /**
     * Bootstrap app.
     *
     * @return void
     */
    protected function bootApp(): void
    {
        $this->container = require __DIR__ . '/../../config/bootstrap.php';
    }

    /**
     * Shutdown app.
     *
     * @return void
     */
    protected function shutdownApp(): void
    {
        $this->container = null;
    }

    /**
     * Get container.
     *
     * @throws RuntimeException
     *
     * @return Container
     */
    protected function getContainer(): Container
    {
        if ($this->container === null) {
            throw new RuntimeException('Container must be initialized');
        }

        $this->container->share(SessionInterface::class, static function () {
            $session = new PhpSession();
            $session->setOptions([
                'cache_expire' => 60,
                'name' => 'app',
                'use_cookies' => false,
                'cookie_httponly' => false,
            ]);

            return $session;
        });

        $this->container->share(LoggerInterface::class, static function () {
            $logger = new Logger('test');

            return $logger->pushHandler(new NullHandler());
        });

        return $this->container;
    }
}
