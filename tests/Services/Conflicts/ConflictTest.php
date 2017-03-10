<?php

namespace Isaac\Services\Conflicts;

use Isaac\TestCase;

class ConflictTest extends TestCase
{
    public function testCanGetHashOfConflict()
    {
        $mods = $this->mods->getMods();

        $conflict = Conflict::forPath('/main.lua', [
            $mods[0],
            $mods[1],
        ]);

        $this->assertEquals(md5('/main.lua1-2'), $conflict->getHash());
    }
}