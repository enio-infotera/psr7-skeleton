<?php

namespace App\Console;

use Exception;
use PDO;
use PDOException;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command.
 */
final class InstallCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container The container
     * @param string|null $name The name
     */
    public function __construct(ContainerInterface $container, ?string $name = null)
    {
        parent::__construct($name);
        $this->container = $container;
    }

    /**
     * Configure.
     *
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->addOption('environment', 'e', InputOption::VALUE_OPTIONAL, 'The target environment.');

        $this->setName('install');
        $this->setDescription('Install a new application');
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input The input
     * @param OutputInterface $output The output
     *
     * @throws RuntimeException
     *
     * @return int integer 0 on success, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $settings = $this->container->get('settings');
        $root = $settings['root'];
        $configPath = $root . '/config';

        $this->createEnvFile($output, $configPath);
        $this->generateRandomSecret($output, $configPath);

        $env = '';
        if ($input->hasOption('environment')) {
            $env = $input->getOption('environment');

            if (!is_string($env) && $env !== null) {
                throw new RuntimeException('Invalid environment');
            }
        }

        try {
            return $this->createNewDatabase($io, $output, $configPath, $root, $env);
        } catch (Exception $exception) {
            $output->writeln(sprintf('<error>Unknown error: %s</error> ', $exception->getMessage()));

            return 1;
        }
    }

    /**
     * Create env.php file.
     *
     * @param OutputInterface $output The output
     * @param string $configPath The config path
     *
     * @return void
     */
    private function createEnvFile(OutputInterface $output, string $configPath): void
    {
        $output->writeln('Create env.php');
        copy($configPath . '/env.example.php', $configPath . '/env.php');
    }

    /**
     * Generate a random secret.
     *
     * @param OutputInterface $output The output
     * @param string $configPath The config path
     *
     * @throws Exception
     *
     * @return void
     */
    private function generateRandomSecret(OutputInterface $output, string $configPath): void
    {
        $output->writeln('Generate random app secret');
        file_put_contents(
            $configPath . '/defaults.php',
            str_replace(
                '{{app_secret}}',
                bin2hex(random_bytes(20)),
                file_get_contents($configPath . '/defaults.php') ?: ''
            )
        );
    }

    /**
     * Create a new database.
     *
     * @param SymfonyStyle $io The io
     * @param OutputInterface $output The output
     * @param string $configPath The config path
     * @param string $root The root path
     * @param string|null $env The environment
     *
     * @return int The error code
     */
    private function createNewDatabase(
        SymfonyStyle $io,
        OutputInterface $output,
        string $configPath,
        string $root,
        ?string $env = null
    ): int {
        if ($env === 'ci') {
            $mySqlHost = '127.0.0.1';
            $mySqlDatabase = 'test';
            $mySqlUsername = 'root';
            $mySqlPassword = '';
        } else {
            // MySQL setup
            $mySqlHost = $io->ask('Enter MySQL host', 'localhost');
            if (!$mySqlHost) {
                $output->writeln('Aborted');

                return 1;
            }
            $mySqlDatabase = $io->ask('Enter MySQL database name', 'prisma');
            if (!$mySqlDatabase) {
                $output->writeln('Aborted');

                return 1;
            }

            $mySqlUsername = $io->ask('Enter MySQL username:', 'root');
            $mySqlPassword = $io->ask('Enter MySQL password:', '', static function ($string) {
                return $string ?: '';
            });
        }

        try {
            $output->writeln('Create database: ' . $mySqlDatabase);

            $pdo = $this->createPdo($mySqlHost, $mySqlUsername, $mySqlPassword);
            $this->createDatabase($pdo, $mySqlDatabase);
            $this->updateDevelopmentSettings(
                $output,
                $mySqlHost,
                $mySqlDatabase,
                $mySqlUsername,
                $mySqlPassword,
                $configPath
            );
            $this->installDatabaseTables($output, $root);
            $this->seedDatabaseTables($output, $root);

            $output->writeln('<info>Setup successfully<info>');

            return 0;
        } catch (PDOException $ex) {
            $output->writeln(sprintf('<error>Database error: %s</error> ', $ex->getMessage()));

            return 1;
        }
    }

    /**
     * Create a PDO object.
     *
     * @param string $host The host
     * @param string $username The username
     * @param string $password The password
     *
     * @return PDO The connection
     */
    private function createPdo(string $host, string $username, string $password): PDO
    {
        return new PDO(
            sprintf('mysql:host=%s;charset=utf8', $host),
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8 COLLATE utf8_unicode_ci',
            ]
        );
    }

    /**
     * Create database.
     *
     * @param PDO $pdo The connection
     * @param string $dbName The database name
     *
     * @return void
     */
    private function createDatabase(PDO $pdo, string $dbName): void
    {
        $dbNameQuoted = $this->quoteName($dbName);
        $pdo->exec(sprintf('CREATE DATABASE IF NOT EXISTS %s;', $dbNameQuoted));
    }

    /**
     * Quote name.
     *
     * @param string $name Table or field name
     *
     * @return string Table or field name
     */
    private function quoteName(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }

    /**
     * Update dev settings.
     *
     * @param OutputInterface $output The output
     * @param string $dbHost The host
     * @param string $dbName The database
     * @param string $username The username
     * @param string $password The password
     * @param string $configPath The config path
     *
     * @return void
     */
    private function updateDevelopmentSettings(
        OutputInterface $output,
        string $dbHost,
        string $dbName,
        string $username,
        string $password,
        string $configPath
    ): void {
        $output->writeln('Update development configuration');
        file_put_contents(
            $configPath . '/development.php',
            str_replace(
                '{{db_host}}',
                $dbHost,
                file_get_contents($configPath . '/development.php') ?: ''
            )
        );

        file_put_contents(
            $configPath . '/development.php',
            str_replace(
                '{{db_database}}',
                $dbName,
                file_get_contents($configPath . '/development.php') ?: ''
            )
        );

        file_put_contents(
            $configPath . '/env.php',
            str_replace(
                '{{db_username}}',
                $username,
                file_get_contents($configPath . '/env.php') ?: ''
            )
        );

        file_put_contents(
            $configPath . '/env.php',
            str_replace(
                '{{db_password}}',
                $password,
                file_get_contents($configPath . '/env.php') ?: ''
            )
        );
    }

    /**
     * Install database.
     *
     * @param OutputInterface $output The outout
     * @param string $root The root path
     *
     * @return void
     */
    private function installDatabaseTables(OutputInterface $output, string $root): void
    {
        $output->writeln('Install database tables');

        chdir($root);
        system('ant migrate-database');
    }

    /**
     * Seed database tables.
     *
     * @param OutputInterface $output The outout
     * @param string $root The root path
     *
     * @return void
     */
    private function seedDatabaseTables(OutputInterface $output, string $root): void
    {
        $output->writeln('Seed database tables');

        chdir($root);
        system('ant seed-database');
    }
}
