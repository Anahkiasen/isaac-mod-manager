<?php

namespace Isaac\Services;

use Isaac\Services\Conflicts\ConflictsHandler;
use Isaac\Services\Environment\Pathfinder;
use Isaac\Services\Mods\ModsManager;
use League\Flysystem\FilesystemInterface;
use League\Tactician\CommandBus;
use Psr\SimpleCache\CacheInterface;

/**
 * @property CommandBus          $bus
 * @property CacheInterface      cache
 * @property ConflictsHandler    conflicts
 * @property FilesystemInterface files
 * @property ModsManager         mods
 * @property Pathfinder          paths
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
            'bus' => CommandBus::class,
            'cache' => CacheInterface::class,
            'conflicts' => ConflictsHandler::class,
            'files' => FilesystemInterface::class,
            'mods' => ModsManager::class,
            'paths' => Pathfinder::class,
        ];

        if (array_key_exists($name, $mapping)) {
            return $this->getContainer()->get($mapping[$name]);
        }
    }
}
