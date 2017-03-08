<?php

namespace Isaac\Providers;

use Cache\Adapter\Filesystem\FilesystemCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Psr\SimpleCache\CacheInterface;

class CacheServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [CacheInterface::class];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->share(CacheInterface::class, function () {
            return new SimpleCacheBridge(new FilesystemCachePool(
                new Filesystem(new Local(__DIR__.'/../..'))
            ));
        });
    }
}
