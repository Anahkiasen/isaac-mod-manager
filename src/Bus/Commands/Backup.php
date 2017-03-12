<?php

namespace Isaac\Bus\Commands;

use Isaac\Bus\OutputAwareInterface;
use Isaac\Bus\OutputAwareTrait;
use Isaac\Services\Environment\Pathfinder;
use Isaac\Services\Mods\ModsManager;
use League\Flysystem\FilesystemInterface;

/**
 * Backs up the resources/ and packed/ folders for later retrieval.
 */
class Backup implements OutputAwareInterface
{
    use OutputAwareTrait;

    /**
     * @param ModsManager                            $mods
     * @param FilesystemInterface                    $files
     * @param \Isaac\Services\Environment\Pathfinder $paths
     */
    public function handle(ModsManager $mods, FilesystemInterface $files, Pathfinder $paths)
    {
        if ($mods->areResourcesBackup()) {
            return;
        }

        $this->getOutput()->writeln('<comment>Making a backup of resources folder, this only has to be done once</comment>');
        $this->getOutput()->writeln('It can take a few minutes so be patient');

        if (!$files->has($paths->getResourcesBackupPath())) {
            $files->copyDirectory($paths->getResourcesPath(), $paths->getResourcesBackupPath());
        }

        if (!$files->has($paths->getPackedBackupPath())) {
            $files->rename($paths->getPackedPath(), $paths->getPackedBackupPath());
        }
    }
}
