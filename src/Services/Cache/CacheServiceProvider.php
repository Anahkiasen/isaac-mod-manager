<?php

namespace Isaac\Services\Cache;

use Cache\Adapter\Filesystem\FilesystemCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

class CacheServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        CacheItemPoolInterface::class,
        CacheInterface::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->share(CacheItemPoolInterface::class, function () {
            $cachePath = sys_get_temp_dir().DS.'imm'.DS.md5(__DIR__);

            return new FilesystemCachePool(
                new Filesystem(new Local($cachePath, LOCK_SH))
            );
        });

        $this->container->share(CacheInterface::class, function () {
            return $this->container->get(SimpleCacheBridge::class);
        });
    }
}
