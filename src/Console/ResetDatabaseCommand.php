<?php

namespace App\Console;

use PDO;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command.
 */
final class ResetDatabaseCommand extends Command
{
    /** @var ContainerInterface */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container container
     * @param string|null $name name
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

        $this->setName('reset-database');
        $this->setDescription('Drop all database tables');
    }

    /**
     * Clear database, drop all tables.
     *
     * @param InputInterface $input input
     * @param OutputInterface $output output
     *
     * @return int integer 0 on success, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pdo = $this->container->get(PDO::class);

        // Drop all tables for the rollback command
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

        $rows = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($rows as $table) {
            $output->writeln(sprintf('<info>Drop table:</info> %s', $table));
            $pdo->exec(sprintf('DROP TABLE `%s`;', $table));
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');

        return 0;
    }
}
