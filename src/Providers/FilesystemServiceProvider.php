<?php

namespace Isaac\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Plugin\ForcedCopy;
use League\Flysystem\Plugin\ListFiles;

class FilesystemServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [FilesystemInterface::class];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->share(FilesystemInterface::class, function () {
            $home = $_SERVER['HOME'];

            $filesystem = new Filesystem(new Local($home));
            $filesystem->addPlugin(new ListFiles());
            $filesystem->addPlugin(new ForcedCopy());

            return $filesystem;
        });
    }
}
