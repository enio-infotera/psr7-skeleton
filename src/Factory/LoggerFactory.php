<?php

namespace App\Factory;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Factory.
 */
final class LoggerFactory implements FactoryInterface
{
    /**
     * @var array
     */
    private $defaults;

    /**
     * Constructor.
     *
     * @param array $defaults The settings
     */
    public function __construct(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * Create file logger.
     *
     * @param string $name The logging channel
     * @param mixed[] $options The options
     *
     * @throws RuntimeException
     *
     * @return LoggerInterface The logger
     */
    public function createLogger(string $name, array $options = []): LoggerInterface
    {
        $name = $name ?? $options['name'] ?? $this->defaults['name'] ?? null;
        $path = $options['path'] ?? $this->defaults['path'] ?? sys_get_temp_dir();
        $filename = $options['filename'] ?? $name ?? $this->defaults['filename'] ?? null;
        $maxFiles = $options['max_files'] ?? $this->defaults['max_files'] ?? 0;
        $level = $options['level'] ?? $this->defaults['level'] ?? Logger::DEBUG;
        $bubble = $options['bubble'] ?? $this->defaults['bubble'] ?? true;
        $filePermission = $options['file_permission'] ?? $this->defaults['filePermission'] ?? null;
        $useLocking = $options['use_locking'] ?? $this->defaults['useLocking'] ?? false;
        $handler = $options['handler'] ?? $this->defaults['handler'] ?? RotatingFileHandler::class;

        $logger = new Logger($name);

        $basename = (string)pathinfo($filename, PATHINFO_FILENAME);
        $extension = (string)pathinfo($filename, PATHINFO_EXTENSION) ?: 'log';
        $filename = sprintf('%s/%s.%s', (string)$path, $basename, $extension);

        if ($handler === RotatingFileHandler::class) {
            $handler = new RotatingFileHandler($filename, $maxFiles, $level, $bubble, $filePermission, $useLocking);
        } else {
            throw new RuntimeException(sprintf('Invalid logging handler: %s', $handler));
        }

        $logger->pushHandler($handler);

        return $logger;
    }
}
