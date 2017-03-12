<?php

namespace Isaac;

use Isaac\Bus\CommandBusServiceProvider;
use Isaac\Console\ClearCache;
use Isaac\Console\Mods\Install;
use Isaac\Console\Mods\Uninstall;
use Isaac\Console\Restore;
use Isaac\Services\Cache\CacheServiceProvider;
use Isaac\Services\ContainerAwareTrait;
use Isaac\Services\Filesystem\FilesystemServiceProvider;
use KevinGH\Amend\Command as SelfUpdate;
use League\Container\Container;
use League\Container\ContainerAwareInterface;
use League\Container\ReflectionContainer;
use Symfony\Component\Console\Application as Console;
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
        CommandBusServiceProvider::class,
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
        SelfUpdate::class,
    ];

    /**
     * @param Container|null $container
     */
    public function __construct(Container $container = null)
    {
        $version = mb_strpos(static::VERSION, 'commit') !== false ? '(dev version)' : static::VERSION;

        parent::__construct('Isaac Mod Manager', $version);

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
            $this->add($this->container->get($command));
        }

        return parent::run($input, $output);
    }
}
