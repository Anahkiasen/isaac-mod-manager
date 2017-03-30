<?php

namespace Isaac\Services\Cache;

use Cache\Adapter\Filesystem\FilesystemCachePool;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Psr\Cache\CacheItemPoolInterface;

class CacheServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        CacheItemPoolInterface::class,
        TaggableCacheInterface::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->share(CacheItemPoolInterface::class, function () {
            $cachePath = sys_get_temp_dir().DS.'isaac-mod-manager';
            $filesystem = new Filesystem(new Local($cachePath, LOCK_SH));

            return new FilesystemCachePool($filesystem);
        });

        $this->container->share(TaggableCacheInterface::class, function () {
            return $this->container->get(TaggableSimpleCacheBridge::class);
        });
    }
}
