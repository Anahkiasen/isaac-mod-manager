<?php

namespace Isaac\Bus\Commands;

use Isaac\TestCase;
use Symfony\Component\Console\Output\NullOutput;

class BackupTest extends TestCase
{
    public function testCanBackupResources()
    {
        $this->bus->handle(new Backup(new NullOutput()));

        $this->assertVirtualFileExists($this->paths->getResourcesBackupPath());
        $this->assertVirtualFileNotExists($this->paths->getPackedPath());
        $this->assertVirtualFileExists($this->paths->getPackedBackupPath());
    }
}