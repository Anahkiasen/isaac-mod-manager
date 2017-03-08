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
    public function getModsPath(): string
    {
        return $this->cache->get('source');
    }

    /**
     * @return string
     */
    public function getGamePath(): string
    {
        return $this->cache->get('destination');
    }

    /**
     * @return string
     */
    public function getPackedPath(): string
    {
        return $this->getGamePath().DIRECTORY_SEPARATOR.'packed';
    }

    /**
     * @return string
     */
    public function getPackedBackupPath(): string
    {
        return $this->getPackedPath().self::PACKED_PREFIX;
    }
}