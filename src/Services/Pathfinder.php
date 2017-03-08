<?php

namespace Isaac\Services;

use Psr\SimpleCache\CacheInterface;

/**
 * Returns various available paths.
 */
class Pathfinder
{
    /**
     * @var string
     */
    const BACKUP_PREFIX = '.pristine';

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
    public function getResourcesPath(): string
    {
        return $this->getGamePath().DS.'resources';
    }

    /**
     * @return string
     */
    public function getResourcesBackupPath(): string
    {
        return $this->getResourcesPath().static::BACKUP_PREFIX;
    }

    /**
     * @return string
     */
    public function getPackedPath(): string
    {
        return $this->getResourcesPath().DS.'packed';
    }

    /**
     * @return string
     */
    public function getPackedBackupPath(): string
    {
        return $this->getPackedPath().static::BACKUP_PREFIX;
    }

    /**
     * @return string
     */
    public function getResourceExtractorPath(): string
    {
        return $this->getGamePath().DS.'tools'.DS.'ResourceExtractor'.DS.'ResourceExtractor.exe';
    }
}
