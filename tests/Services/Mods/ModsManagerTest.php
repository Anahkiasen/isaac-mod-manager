<?php

namespace Isaac\Services\Mods;

use Isaac\TestCase;

class ModsManagerTest extends TestCase
{
    public function testCanCheckIfResourcesAreBackedUp()
    {
        $this->assertFalse($this->mods->areResourcesBackup());

        $this->files->createDir($this->paths->getResourcesBackupPath());
        $this->files->createDir($this->paths->getPackedBackupPath());
        $this->assertTrue($this->mods->areResourcesBackup());
    }

    public function testCanCheckIfResourcesAreExtracted()
    {
        $this->assertTrue($this->mods->areResourcesExtracted());

        $this->files->delete($this->paths->getResourcesPath().'/achievements.xml');
        $this->assertFalse($this->mods->areResourcesExtracted());
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
        $mods = $this->mods->findMods(['foo', '2', 2, 99]);

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

    public function testCanInstallMod()
    {
        $this->mods->installMod($this->mods->findModById(3));

        $this->assertVirtualFileExists($this->paths->getResourcesPath().'/scripts/main.lua');
        $this->assertVirtualFileExists($this->paths->getResourcesPath().'/resources/gfx/foo.png');
        $this->assertEquals('main'.PHP_EOL.'lua', $this->files->read($this->paths->getResourcesPath().'/scripts/main.lua'));
        $this->assertVirtualFileNotExists($this->paths->getResourcesPath().'/metadata.xml');
    }

    public function testCanRemoveMod()
    {
        // Add root file to mod
        $this->files->put($this->paths->getModsPath().'/3/foo.png', '');

        $mod = $this->mods->findModById(3);

        $this->mods->installMod($mod);
        $this->assertVirtualFileExists($this->paths->getResourcesPath().'/foo.png');

        $this->files->put($this->paths->getResourcesBackupPath().'/foo.png', 'foobar');
        $this->mods->removeMod($mod);
        $this->assertEquals('foobar', $this->files->read($this->paths->getResourcesPath().'/foo.png'));
    }

    public function testCanRestoreMainLuaFile()
    {
        $this->files->put($this->paths->getMainLuaBackupPath(), 'foo');
        $this->files->put($this->paths->getMainLuaPath(), 'bar');

        $this->mods->restoreMainLua();
        $this->assertEquals('foo', $this->files->read($this->paths->getMainLuaPath()));

        $this->files->delete($this->paths->getMainLuaBackupPath());
        $this->mods->restoreMainLua();
    }
}