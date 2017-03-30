<?php

namespace Isaac\Bus\Commands;

use Isaac\Bus\OutputAwareInterface;
use Isaac\Bus\OutputAwareTrait;
use Isaac\Services\Environment\Pathfinder;
use League\Flysystem\FilesystemInterface;

/**
 * Backs up the resources/ and packed/ folders for later retrieval.
 */
class Backup implements OutputAwareInterface
{
    use OutputAwareTrait;

    /**
     * @param FilesystemInterface                    $files
     * @param \Isaac\Services\Environment\Pathfinder $paths
     */
    public function handle(FilesystemInterface $files, Pathfinder $paths)
    {
        // Backup resources
        if (!$files->has($paths->getResourcesBackupPath())) {
            $this->backupResources($files, $paths);
        }

        // Backup packed
        if (!$files->has($paths->getPackedBackupPath())) {
            $this->backupPacked($files, $paths);
        }
    }

    /**
     * @param FilesystemInterface $files
     * @param Pathfinder          $paths
     */
    protected function backupResources(FilesystemInterface $files, Pathfinder $paths)
    {
        $this->getOutput()->writeln('<comment>Making a backup of resources folder, this only has to be done once</comment>');
        $this->getOutput()->writeln('It can take a few minutes so be patient');
        $files->copyDirectory($paths->getResourcesPath(), $paths->getResourcesBackupPath(), $this->getOutput());
    }

    /**
     * @param FilesystemInterface $files
     * @param Pathfinder          $paths
     */
    protected function backupPacked(FilesystemInterface $files, Pathfinder $paths)
    {
        $files->rename($paths->getPackedPath(), $paths->getPackedBackupPath());
    }
}
