<?php

namespace Isaac\Services\Mods;

use Isaac\Services\Conflicts\Conflict;
use Isaac\Services\Conflicts\Resolutions;
use Isaac\TestCase;

class ModTest extends TestCase
{
    public function testCanListFiles()
    {
        $mod = new Mod($this->paths->getModsPath().'/3');
        $mod->setFilesystem($this->files);
        $files = $mod->listFiles();

        $this->assertContains('main.lua', $files->pluck('basename'));
    }

    public function testCanListFilesWithConflictsResolved()
    {
        $this->files->put('/mods/4/resources/gfx/foo.png', '');
        $mods = $this->mods->getMods();

        $mod = $mods[2];
        $otherMod = $mods[3];

        $resolutions = new Resolutions([
            Conflict::forPath('/resources/gfx/foo.png', [$mod, $otherMod])->resolve($mod->getId()),
        ]);

        $mod->setResolutions($resolutions);
        $otherMod->setResolutions($resolutions);

        $this->assertContains('foo.png', $mod->listFiles()->pluck('basename'));
        $this->assertNotContains('foo.png', $otherMod->listFiles()->pluck('basename'));
    }
}