<?php

namespace Isaac\Services;

use League\Flysystem\FilesystemInterface;

/**
 * Manages the installation/uninstallation of mods and of IMM.
 */
class ModsManager
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var Pathfinder
     */
    protected $paths;

    /**
     * @param FilesystemInterface $filesystem
     * @param Pathfinder          $paths
     */
    public function __construct(FilesystemInterface $filesystem, Pathfinder $paths)
    {
        $this->filesystem = $filesystem;
        $this->paths = $paths;
    }

    ////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////// PACKING ////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * Check whether the game is already unpacked or not.
     *
     * @return bool
     */
    public function areResourcesBackup(): bool
    {
        return $this->filesystem->has($this->paths->getResourcesBackupPath());
    }

    /**
     * Unpack the game's resources.
     */
    public function backup()
    {
        $this->filesystem->copyDirectory(
            $this->paths->getResourcesPath(),
            $this->paths->getResourcesBackupPath()
        );

        // Rename "packed" folder
        $this->filesystem->rename(
            $this->paths->getPackedPath(),
            $this->paths->getPackedBackupPath()
        );
    }

    /**
     * Restores Isaac to a pristine modded version.
     */
    public function repack()
    {
        // Rename back the "packed" folder
        if ($this->filesystem->has($this->paths->getPackedBackupPath())) {
            $this->filesystem->rename(
                $this->paths->getPackedBackupPath(),
                $this->paths->getPackedPath()
            );
        }

        // Delete contents of resource folder
        foreach ($this->filesystem->listContents($this->paths->getResourcesPath()) as $file) {
            if ($file['basename'] !== 'packed') {
                $file['type'] === 'dir'
                    ? $this->filesystem->deleteDir($file['path'])
                    : $this->filesystem->delete($file['path']);
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////// MODS /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * Get all workshop mods currently downloaded.
     *
     * @return Mod[]
     */
    public function getMods(): array
    {
        $mods = $this->filesystem->listContents($this->paths->getModsPath());
        foreach ($mods as &$mod) {
            $mod = new Mod($mod);
        }

        return $mods;
    }

    /**
     * Get all mods that are graphical only.
     *
     * @return Mod[]
     */
    public function getGraphicalMods(): array
    {
        return array_filter($this->getMods(), function (Mod $mod) {
            return !$this->filesystem->has($mod->getPath('main.lua')) && $this->filesystem->has($mod->getPath('resources'));
        });
    }

    /**
     * Install a given mod.
     *
     * @param Mod $mod
     */
    public function installMod(Mod $mod)
    {
        $resourcesPath = $mod->getPath('resources');
        foreach ($this->filesystem->listFiles($resourcesPath, true) as $file) {
            $relativePath = str_replace($mod->getPath(), null, $file['path']);
            $destination = $this->paths->getGamePath().$relativePath;
            $this->filesystem->forceCopy($file['path'], $destination);
        }
    }
}
