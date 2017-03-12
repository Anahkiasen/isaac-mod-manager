<?php

namespace Isaac\Console\Commands;

use Isaac\TestCase;
use Psr\SimpleCache\CacheInterface;

class ClearCacheTest extends TestCase
{
    public function testCanClearCache()
    {
        $cache = $this->prophesize(CacheInterface::class);
        $cache->has('selfupdate')->willReturn(false);
        $cache->clear()->shouldBeCalled();

        $this->executeCommand(ClearCache::class);
    }
}