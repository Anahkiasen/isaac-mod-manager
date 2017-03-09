<?php

namespace Isaac\Services;

use Isaac\Services\Mods\ModsManager;
use League\Flysystem\FilesystemInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @property CacheInterface cache
 * @property FilesystemInterface files
 * @property ModsManager mods
 * @property Pathfinder paths
 */
trait ContainerAwareTrait
{
    use \League\Container\ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function __get(string $name)
    {
        $mapping = [
            'cache' => CacheInterface::class,
            'files' => FilesystemInterface::class,
            'mods' => ModsManager::class,
            'paths' => Pathfinder::class,
        ];

        if (array_key_exists($name, $mapping)) {
            return $this->getContainer()->get($mapping[$name]);
        }
    }
}
