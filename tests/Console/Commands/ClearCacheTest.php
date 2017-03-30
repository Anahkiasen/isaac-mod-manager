<?php

namespace Isaac\Console\Commands;

use Isaac\Services\Cache\TaggableCacheInterface;
use Isaac\Services\Conflicts\ConflictsHandler;
use Isaac\TestCase;

class ClearCacheTest extends TestCase
{
    public function testCanClearCache()
    {
        $cache = $this->prophesize(TaggableCacheInterface::class);
        $cache->has('selfupdate')->willReturn(false);
        $cache->clear()->shouldBeCalled();

        $this->executeCommand(ClearCache::class);
    }

    public function testCanClearOnlyConflictsCache()
    {
        $cache = $this->prophesize(TaggableCacheInterface::class);
        $cache->has('selfupdate')->willReturn(false);
        $cache->clear()->shouldNotBeCalled();
        $cache->invalidateTag(ConflictsHandler::CACHE_TAG)->shouldBeCalled();

        $this->executeCommand(ClearCache::class, ['--conflicts' => true]);
    }
}