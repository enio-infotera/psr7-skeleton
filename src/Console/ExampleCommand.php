<?php

namespace App\Console;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command.
 */
final class ExampleCommand extends Command
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

        $this->addOption('environment', 'e', InputOption::VALUE_REQUIRED, 'The target environment.');

        $this->setName('example');
        $this->setDescription('A sample command');
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input input
     * @param OutputInterface $output output
     *
     * @return int integer 0 on success, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $root = $this->container->get('settings')['root'];

        $output->writeln(sprintf('<info>The project root path is:</info> %s', $root));

        return 0;
    }
}
