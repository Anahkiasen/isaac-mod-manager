<?php

namespace Isaac\Providers;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use Isaac\Services\Filesystem\CopyDirectory;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Plugin\ForcedCopy;
use League\Flysystem\Plugin\ListFiles;
use League\Flysystem\Vfs\VfsAdapter;
use Psr\SimpleCache\CacheInterface;
use VirtualFileSystem\FileSystem as VirtualFilesystem;

class TestingServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        FilesystemInterface::class,
        CacheInterface::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->share(FilesystemInterface::class, function () {
            $adapter = new VfsAdapter(new VirtualFilesystem());

            $filesystem = new Filesystem($adapter);
            $filesystem->addPlugin(new ListFiles());
            $filesystem->addPlugin(new ForcedCopy());
            $filesystem->addPlugin(new CopyDirectory());

            return $filesystem;
        });

        $this->container->share(CacheInterface::class, function () {
            return new SimpleCacheBridge(new ArrayCachePool());
        });
    }
}