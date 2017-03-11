<?php

namespace Isaac\Services\Conflicts;

use Illuminate\Support\Collection;
use Isaac\TestCase;

class ConflictsHandlerTest extends TestCase
{
    public function testCanFindConflicts()
    {
        $mods = $this->mods->getMods();
        $conflicts = $this->conflicts->findUnresolvedConflicts($mods);

        $this->assertInstanceOf(Collection::class, $conflicts);
        $this->assertInstanceOf(Conflict::class, $conflicts->first());
        $this->assertEquals([
            Conflict::forPath('/main.lua', [$mods[2], $mods[3]]),
        ], $conflicts->all());
    }

    public function testCanIgnoredAlreadySolvedConflicts()
    {
        $mods = $this->mods->getMods();

        $conflicts = $this->conflicts->findUnresolvedConflicts($mods);
        $this->cache->set($conflicts->first()->getHash(), $conflicts->first()->first()->getId());

        $conflicts = $this->conflicts->findUnresolvedConflicts($mods);
        $this->assertInstanceOf(Collection::class, $conflicts);
        $this->assertEmpty($conflicts);
    }

    public function testCanResolveConflict()
    {
        $mods = $this->mods->getMods();

        $conflicts = $this->conflicts->findUnresolvedConflicts($mods);
        $conflicts[0] = $this->conflicts->resolve($conflicts[0], $conflicts[0]->first()->getId());

        $conflicts = $this->conflicts->findUnresolvedConflicts($mods);
        $this->assertInstanceOf(Collection::class, $conflicts);
        $this->assertEmpty($conflicts);
    }

    public function testCanIgnoreInvalidResolution()
    {
        $mods = $this->mods->getMods();
        $mods = $this->conflicts->findAndResolve($mods, 'sdfsdfds');

        $this->assertCount(4, $mods);
    }
}