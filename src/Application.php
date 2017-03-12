<?php

namespace Isaac;

use Isaac\Bus\CommandBusServiceProvider;
use Isaac\Console\Commands\ClearCache;
use Isaac\Console\Commands\Mods\Install;
use Isaac\Console\Commands\Mods\Uninstall;
use Isaac\Console\Commands\Restore;
use Isaac\Console\Commands\SelfUpdate;
use Isaac\Console\ConsoleServiceProvider;
use Isaac\Services\Cache\CacheServiceProvider;
use Isaac\Services\ContainerAwareTrait;
use Isaac\Services\Filesystem\FilesystemServiceProvider;
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
        CacheServiceProvider::class,
        CommandBusServiceProvider::class,
        FilesystemServiceProvider::class,
        ConsoleServiceProvider::class,
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
        $version = self::isDevelopmentVersion() ? '(dev version)' : static::VERSION;

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
     * @return bool
     */
    public static function isDevelopmentVersion(): bool
    {
        return mb_strpos(static::VERSION, 'commit') !== false;
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
