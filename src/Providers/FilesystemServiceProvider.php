<?php

namespace Isaac\Providers;

use Isaac\Services\Filesystem\CopyDirectory;
use Isaac\Services\Filesystem\LooseLocal;
use League\Container\ServiceProvider\AbstractServiceProvider;
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
            $filesystem = new Filesystem(new LooseLocal());
            $filesystem->addPlugin(new ListFiles());
            $filesystem->addPlugin(new ForcedCopy());
            $filesystem->addPlugin(new CopyDirectory());

            return $filesystem;
        });
    }
}
