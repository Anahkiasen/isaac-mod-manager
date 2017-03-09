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
}