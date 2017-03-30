<?php

namespace Isaac\Bus\Commands;

use Isaac\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;

class BackupTest extends TestCase
{
    public function testCanBackupResources()
    {
        $this->bus->handle(new Backup());

        $this->assertVirtualFileExists($this->paths->getResourcesBackupPath());
        $this->assertVirtualFileNotExists($this->paths->getPackedPath());
        $this->assertVirtualFileExists($this->paths->getPackedBackupPath());
    }

    public function testDoesntBackupTwice()
    {
        $this->files->createDir($this->paths->getResourcesBackupPath());

        $output = $this->prophesize(SymfonyStyle::class);
        $output->writeln()->shouldNotBeCalled();

        $command = new Backup();
        $command->setOutput($output->reveal());
        $this->bus->handle($command);
    }
}