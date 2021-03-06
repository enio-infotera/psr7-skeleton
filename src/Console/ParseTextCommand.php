<?php

namespace App\Console;

use Exception;
use FilesystemIterator;
use Gettext\Merge;
use Gettext\Translations;
use MultipleIterator;
use Odan\Twig\TwigCompiler;
use Psr\Container\ContainerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment as Twig;

/**
 * Command.
 */
final class ParseTextCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var MultipleIterator
     */
    private $iterator;

    /**
     * @var array
     */
    private $targets = [];

    /**
     * @var string
     */
    private $regex;

    /**
     * @var array
     */
    private $suffixes = [
        '.blade.php' => 'Blade',
        '.csv' => 'Csv',
        '.jed.json' => 'Jed',
        '.js' => 'JsCode',
        //'.json' => 'Json',
        '.mo' => 'Mo',
        '.php' => ['PhpCode', 'PhpArray'],
        '.po' => 'Po',
        '.pot' => 'Po',
        '.twig' => 'Twig',
        '.xliff' => 'Xliff',
        '.yaml' => 'Yaml',
    ];

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
        $this->iterator = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
    }

    /**
     * Configure.
     *
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName('parse-text');
        $this->setDescription('Parse text');
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input The input
     * @param OutputInterface $output The output
     *
     * @throws Exception
     *
     * @return int integer 0 on success, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;

        $result = $this->compileTwig();
        if ($result) {
            return $result;
        }

        $result = $this->scanText();
        if ($result) {
            return $result;
        }

        return 0;
    }

    /**
     * Execute command.
     *
     * @throws Exception
     *
     * @return int integer 0 on success, or an error code
     */
    private function compileTwig(): int
    {
        $this->output->write('Compiling Twig templates... ');

        $twig = $this->container->get(Twig::class);

        $settings = $this->container->get('settings');
        $cachePath = $settings['twig']['cache_path'];

        // Get the Twig Environment instance from the Twig View instance
        $twig->setCache($cachePath);

        // Compile all Twig templates into cache directory
        $compiler = new TwigCompiler($twig, $cachePath);
        $compiler->compile();

        $this->output->write('<info>Done</info>', true);

        return 0;
    }

    /**
     * Execute command.
     *
     * @throws Exception
     *
     * @return int integer 0 on success, or an error code
     */
    private function scanText(): int
    {
        $this->output->write('Scanning text...', true);

        $currentDir = getcwd() ?: __DIR__;
        chdir(__DIR__ . '/../..');

        $this->extract('src')
            ->extract('tmp/twig-cache')
            ->extract('templates/', '/.*\.js/')
            ->extract('public/js/', '/.*\.js/')
            ->generate('resources/locale/de_DE_messages.po')
            ->generate('resources/locale/de_DE_messages.mo')
            // Add more mo and po files here
            ->process();

        $this->output->write('Done', true);

        chdir($currentDir);

        return 0;
    }

    /**
     * Run the task.
     *
     * @return int Error level
     */
    private function process(): int
    {
        foreach ($this->targets as $targets) {
            /** @var SplFileInfo $target */
            $target = $targets[0];
            $translations = new Translations();
            $this->scan($translations);

            if (is_file($target)) {
                $fn = $this->getFunctionName('from', $target, 'File', 1);
                $newTranslations = Translations::$fn($target);
                $translations = $this->addFuzzyFlags($newTranslations, $translations);
                // de_DE
                $filename = basename($target);
                $language = substr($filename, 0, 5);
                $this->output->writeln(sprintf('Set language: %s', $language));
                $translations->setLanguage($language);
                $translations = $translations->mergeWith(
                    $newTranslations,
                    Merge::TRANSLATION_OVERRIDE | Merge::HEADERS_OVERRIDE | Merge::COMMENTS_THEIRS | Merge::FLAGS_THEIRS
                );
            }

            foreach ($targets as $target) {
                $fn = $this->getFunctionName('to', $target, 'File', 1);
                $dir = dirname($target);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $translations->$fn($target);

                $file = realpath($target);
                $this->output->write(sprintf('Gettext exported to %s', $file), true);
            }
        }

        $this->output->write('<info>All gettext generated successfuly!</info>', true);

        return 0;
    }

    /**
     * Execute the scan.
     *
     * @param Translations $translations The translations
     *
     * @return void
     */
    private function scan(Translations $translations): void
    {
        foreach ($this->iterator as $each) {
            /** @var SplFileInfo|null $file */
            foreach ($each as $file) {
                if ($file === null || !$file->isFile()) {
                    continue;
                }

                $target = $file->getPathname();
                $fn = $this->getFunctionName('addFrom', $target, 'File');

                if ($fn) {
                    $translations->$fn($target);
                }
            }
        }
    }

    /**
     * Get the format based in the extension.
     *
     * @param string $prefix Prefix
     * @param string $file File
     * @param string $suffix Suffix
     * @param int $key Key
     *
     * @return string|null
     */
    private function getFunctionName(string $prefix, string $file, string $suffix, int $key = 0): ?string
    {
        if (preg_match($this->getRegex(), strtolower($file), $matches)) {
            $format = $this->suffixes[$matches[1]];
            if (is_array($format)) {
                $format = $format[$key];
            }

            return sprintf('%s%s%s', $prefix, $format, $suffix);
        }

        return null;
    }

    /**
     * Returns the regular expression to detect the file format.
     *
     * @return string The regex
     */
    private function getRegex(): string
    {
        if ($this->regex === null) {
            $this->regex = '/(' . str_replace('.', '\\.', implode('|', array_keys($this->suffixes))) . ')$/';
        }

        return $this->regex;
    }

    /**
     * Add a new target.
     *
     * @param string $path The path
     *
     * @return self
     */
    public function generate(string $path): self
    {
        $this->targets[] = func_get_args();

        return $this;
    }

    /**
     * Add a new source folder.
     *
     * @param string $path Path
     * @param string|null $regex Regex
     *
     * @return self
     */
    public function extract(string $path, ?string $regex = null): self
    {
        $directory = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory);
        if ($regex) {
            $iterator = new RegexIterator($iterator, $regex);
        }
        $this->iterator->attachIterator($iterator);

        return $this;
    }

    /**
     * Add fuzzy flag for new translations.
     *
     * @param Translations $from From translations
     * @param Translations $to To translations
     *
     * @return Translations The translations with fuzzy flag
     */
    private function addFuzzyFlags(Translations $from, Translations $to): Translations
    {
        foreach ($to as $translation) {
            if (!$from->find($translation)) {
                $translation->addFlag('fuzzy');
            }
        }

        return $to;
    }
}
