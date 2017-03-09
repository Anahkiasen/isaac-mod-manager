<?php

namespace Isaac\Services\Conflicts;

use Illuminate\Support\Collection;
use Isaac\TestCase;

class ConflictsHandlerTest extends TestCase
{
    public function testCanFindConflicts()
    {
        $mods = $this->mods->getMods();
        $mapping = $this->conflicts->findConflicts($mods);

        $this->assertInstanceOf(Collection::class, $mapping);
        $this->assertInstanceOf(Conflict::class, $mapping->first());
        $this->assertEquals([
            '/main.lua' => new Conflict([$mods[2], $mods[3]]),
        ], $mapping->all());
    }
}