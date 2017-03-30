<?php

namespace Isaac\Providers;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Isaac\Services\Cache\TaggableCacheInterface;
use Isaac\Services\Cache\TaggableSimpleCacheBridge;
use Isaac\Services\Filesystem\CopyDirectory;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Plugin\ForcedCopy;
use League\Flysystem\Plugin\ListFiles;
use League\Flysystem\Vfs\VfsAdapter;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use VirtualFileSystem\FileSystem as VirtualFilesystem;

class TestingServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        FilesystemInterface::class,
        TaggableCacheInterface::class,
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

        $this->container->share(TaggableCacheInterface::class, function () {
            return new TaggableSimpleCacheBridge(new ArrayCachePool());
        });

        $this->container->share(SymfonyStyle::class, function () {
            return new SymfonyStyle(new ArrayInput([]), new NullOutput());
        });
    }
}