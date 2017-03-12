<?php

namespace Isaac\Bus\Commands;

use Isaac\Services\Environment\Pathfinder;
use League\Flysystem\FilesystemInterface;

/**
 * Restore Isaac to its original state.
 */
class Restore
{
    /**
     * @param FilesystemInterface $files
     * @param Pathfinder          $paths
     */
    public function handle(FilesystemInterface $files, Pathfinder $paths)
    {
        // Rename back the "packed" folder
        if ($files->has($paths->getPackedBackupPath())) {
            $files->rename(
                $paths->getPackedBackupPath(),
                $paths->getPackedPath()
            );
        }

        // Delete resources backup folder
        if ($files->has($paths->getResourcesBackupPath())) {
            $files->deleteDir($paths->getResourcesBackupPath());
        }

        // Delete contents of resource folder
        foreach ($files->listContents($paths->getResourcesPath()) as $file) {
            if ($file['basename'] !== 'packed') {
                $file['type'] === 'dir'
                    ? $files->deleteDir($file['path'])
                    : $files->delete($file['path']);
            }
        }
    }
}
