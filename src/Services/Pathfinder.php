<?php

namespace Isaac\Services;

use Psr\SimpleCache\CacheInterface;

class Pathfinder
{
    /**
     * @var string
     */
    const PACKED_PREFIX = '-backup';

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return string
     */
    public function getModsFolder(): string
    {
        return $this->cache->get('source');
    }

    /**
     * @return string
     */
    public function getGameFolder(): string
    {
        return $this->cache->get('destination');
    }

    /**
     * @return string
     */
    public function getPackedFolder(): string
    {
        return $this->getGameFolder().DIRECTORY_SEPARATOR.'packed';
    }

    /**
     * @return string
     */
    public function getPackedFolderBackup(): string
    {
        return $this->getPackedFolder().self::PACKED_PREFIX;
    }
}