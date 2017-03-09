<?php

namespace Isaac\Services\Mods;

use Isaac\TestCase;

class ModsManagerTest extends TestCase
{
    public function testCanCheckIfResourcesAreBackedUp()
    {
        $this->assertFalse($this->mods->areResourcesBackup());

        $this->files->createDir('/game/resources.pristine');
        $this->assertTrue($this->mods->areResourcesBackup());
    }

    public function testCanCheckIfResourcesAreExtracted()
    {
        $this->assertTrue($this->mods->areResourcesExtracted());

        $this->files->delete('/game/resources/achievements.xml');
        $this->assertFalse($this->mods->areResourcesExtracted());
    }

    public function testCanBackupResources()
    {
        $this->mods->backup();

        $this->assertVirtualFileExists($this->paths->getResourcesBackupPath());
        $this->assertVirtualFileNotExists($this->paths->getPackedPath());
        $this->assertVirtualFileExists($this->paths->getPackedBackupPath());
    }

    public function testCanFindModById()
    {
        $mod = $this->mods->findModById(1);

        $this->assertInstanceOf(Mod::class, $mod);
        $this->assertEquals(1, $mod->getId());
    }

    public function testCanWarnAboutNotFoundMod()
    {
        $this->expectException(ModNotFoundException::class);

        $this->mods->findModById(987654321);
    }

    public function testCanFindModByName()
    {
        $mod = $this->mods->findModByName('FOO');

        $this->assertInstanceOf(Mod::class, $mod);
        $this->assertEquals(1, $mod->getId());
    }

    public function testCanFindModsByNameOrId()
    {
        $mods = $this->mods->findMods(['foo', '2', 2, 3]);

        $this->assertCount(2, $mods);
        $this->assertEquals('foobar', $mods[0]->getName());
        $this->assertEquals('barbaz', $mods[1]->getName());
    }

    public function testCanGetGraphicalModsOnly()
    {
        $graphical = $this->mods->getGraphicalMods();
        $modNames = $graphical->map->getName();

        $this->assertContains('foobar', $modNames);
        $this->assertNotContains('lua', $modNames);
    }

    public function testCanGetLuaModsOnly()
    {
        $graphical = $this->mods->getLuaMods();
        $modNames = $graphical->map->getName();

        $this->assertNotContains('foobar', $modNames);
        $this->assertContains('lua', $modNames);
    }

    public function testCanFindConflicts()
    {
        $mods = $this->mods->getMods();
        $mapping = $this->mods->findConflicts($mods);

        $this->assertEquals([
            '/main.lua' => collect([$mods[2], $mods[3]]),
        ], $mapping->all());
    }
}