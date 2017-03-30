<?php

namespace Isaac\Services\Cache;

use Isaac\TestCase;

class TaggableSimpleCacheBridgeTest extends TestCase
{
    public function testCanSetTagsOnItems()
    {
        $this->cache->setWithTags('foo', 'foo', 'foo');
        $this->cache->setWithTags('bar', 'bar', 'bar');
        $this->cache->setWithTags('baz', 'baz');

        $this->cache->invalidateTag('foo');
        $this->assertNull($this->cache->get('foo'));
        $this->assertEquals('baz', $this->cache->get('baz'));
        $this->assertEquals('bar', $this->cache->get('bar'));
    }
}