<?php

namespace Isaac\Services\Mods;

use Illuminate\Support\Collection;
use Psr\SimpleCache\CacheInterface;

class ConflictsResolver
{
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
     * @param Collection $mods
     */
    public function resolveConflicts(Collection $mods)
    {

    }
}