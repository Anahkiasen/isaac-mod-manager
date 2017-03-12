<?php

namespace Isaac\Services\Mods;

use Isaac\Services\Conflicts\Conflict;
use Isaac\Services\Conflicts\Resolutions;
use Isaac\TestCase;

class ModTest extends TestCase
{
    public function testCanListFiles()
    {
        $files = $this->getMockedMod(3)->listFiles();

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

    public function testCanGetMetadata()
    {
        $mod = $this->getMockedMod(3);

        $this->assertEquals('lua', $mod->getMetadata('name'));
        $this->assertEquals(['id' => '3', 'name' => 'lua'], $mod->getMetadata());
    }

    public function testDoesntCrashIfCantParseMetadata()
    {
        $mod = $this->getMockedMod(3);
        $this->files->put($mod->getPath('metadata.xml'), 'sdfdgsdgdsfgsdf');

        $this->assertEquals([], $mod->getMetadata());
    }

    public function testDoesntCrashIfNoMetadata()
    {
        $mod = $this->getMockedMod(3);
        $this->files->delete($mod->getPath('metadata.xml'));

        $this->assertEquals([], $mod->getMetadata());
    }
}