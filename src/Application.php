<?php

namespace Isaac;

use Isaac\Commands\ClearCache;
use Isaac\Commands\Mods\Install;
use Isaac\Commands\Mods\Uninstall;
use Isaac\Commands\Restore;
use Isaac\Services\Cache\CacheServiceProvider;
use Isaac\Services\ContainerAwareTrait;
use Isaac\Services\Filesystem\FilesystemServiceProvider;
use League\Container\Container;
use League\Container\ContainerAwareInterface;
use League\Container\ReflectionContainer;
use Symfony\Component\Console\Application as Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * This is the main class of the CLI application,
 * it registers the commands and dependencies, as well
 * as dispatches calls to said commands.
 */
class Application extends Console implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    const VERSION = '@commit@';

    /**
     * @var array
     */
    protected $providers = [
        CacheServiceProvider::class,
        FilesystemServiceProvider::class,
    ];

    /**
     * @var array
     */
    protected $commands = [
        Install::class,
        Uninstall::class,
        Restore::class,
        ClearCache::class,
    ];

    /**
     * @param Container $container
     */
    public function __construct(Container $container = null)
    {
        parent::__construct('Isaac Mod Manager', static::VERSION);

        // Setup container
        $container = $container ?: new Container();
        $container->delegate(new ReflectionContainer());
        foreach ($this->providers as $provider) {
            $container->addServiceProvider($provider);
        }

        // Register CLI commands
        $this->setContainer($container);
    }

    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        // Register commands with the CLI application
        foreach ($this->commands as $command) {
            /** @var Command $command */
            $command = $this->container->get($command);
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($this->container);
            }

            $this->add($command);
        }

        return parent::run($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        $version = parent::getVersion();

        return $version === '@commit@' ? '(dev version)' : $version;
    }
}
