<?php

namespace Isaac;

use Isaac\Commands\Install;
use Isaac\Commands\Restore;
use Isaac\Commands\Uninstall;
use Isaac\Providers\CacheServiceProvider;
use Isaac\Providers\FilesystemServiceProvider;
use League\Container\Container;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use League\Container\ReflectionContainer;
use Symfony\Component\Console\Application as Console;
use Symfony\Component\Console\Command\Command;

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
    const VERSION = '0.1.0';

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
        foreach ($this->commands as $command) {
            /** @var Command $command */
            $command = $this->container->get($command);
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($this->container);
            }

            $this->add($command);
        }
    }
}
