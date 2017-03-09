<?php

namespace Isaac\Services\Conflicts;

use Illuminate\Support\Collection;
use Isaac\TestCase;

class ConflictsHandlerTest extends TestCase
{
    public function testCanFindConflicts()
    {
        $mods = $this->mods->getMods();
        $conflicts = $this->conflicts->findConflicts($mods);

        $this->assertInstanceOf(Collection::class, $conflicts);
        $this->assertInstanceOf(Conflict::class, $conflicts->first());
        $this->assertEquals([
            Conflict::forPath('/main.lua', [$mods[2], $mods[3]]),
        ], $conflicts->all());
    }

    public function testCanIgnoredAlreadySolvedConflicts()
    {
        $mods = $this->mods->getMods();

        $conflicts = $this->conflicts->findConflicts($mods);
        $this->cache->set($conflicts->first()->getHash(), 1);

        $conflicts = $this->conflicts->findConflicts($mods);
        $this->assertInstanceOf(Collection::class, $conflicts);
        $this->assertEmpty($conflicts);
    }

    public function testCanResolveConflict()
    {
        $mods = $this->mods->getMods();

        $conflicts = $this->conflicts->findConflicts($mods);
        $conflicts[0] = $this->conflicts->resolve($conflicts[0], 1);

        $conflicts = $this->conflicts->findConflicts($mods);
        $this->assertInstanceOf(Collection::class, $conflicts);
        $this->assertEmpty($conflicts);
    }

    public function testCanComputeModsQueue()
    {
        $mods = $this->mods->getMods();
        $mods = $this->conflicts->findAndResolve($mods, 4);

        $this->assertCount(3, $mods);
        $this->assertEquals([1, 2, 4], $mods->map->getId()->values()->all());
    }

    public function testCanIgnoreInvalidResolution()
    {
        $mods = $this->mods->getMods();
        $mods = $this->conflicts->findAndResolve($mods, 'sdfsdfds');

        $this->assertCount(4, $mods);
    }
}