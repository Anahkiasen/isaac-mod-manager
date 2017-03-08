<?php

namespace Isaac\Services\Mods;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Isaac\Services\Pathfinder;
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
     * @return bool
     */
    public function areResourcesExtracted(): bool
    {
        return $this->filesystem->has($this->paths->getResourcesPath().DS.'achievements.xml');
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

    ////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////// MODS /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * Get a collection of Mod instances by ID.
     *
     * @param int[] $mods
     *
     * @return Collection|Mod[]
     */
    public function findModsById(array $mods): Collection
    {
        return collect($mods)->unique()->map(function ($modId) {
            return $this->findModById($modId);
        });
    }

    /**
     * Get a mod instance by its ID.
     *
     * @param int $modId
     *
     * @return Mod
     */
    public function findModById(int $modId): Mod
    {
        foreach ($this->getGraphicalMods() as $mod) {
            if ($mod->isMod($modId)) {
                return $mod;
            }
        }

        throw new InvalidArgumentException('Cannot find a mod with ID: '.$modId);
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
            $filepath = $file['path'];

            $this->filesystem->forceCopy($filepath, $this->paths->getModeFileInResources($mod, $filepath));
        }
    }

    /**
     * Remove a particular mod.
     *
     * @param int|Mod $mod
     */
    public function removeMod($mod)
    {
        $mod = $mod instanceof Mod ? $mod : $this->findModById($mod);
        foreach ($this->filesystem->listFiles($mod->getPath('resources'), true) as $file) {
            $this->filesystem->forceCopy(
                $this->paths->getModeFileInResourcesBackup($mod, $file['path']),
                $this->paths->getModeFileInResources($mod, $file['path'])
            );
        }
    }

    /**
     * Remove one or more mods.
     *
     * @param Collection $mods
     */
    public function removeMods(Collection $mods)
    {
        foreach ($mods as $mod) {
            $this->removeMod($mod);
        }
    }

    /**
     * Restores Isaac to a pristine non-modded version.
     */
    public function restore()
    {
        // Rename back the "packed" folder
        if ($this->filesystem->has($this->paths->getPackedBackupPath())) {
            $this->filesystem->rename(
                $this->paths->getPackedBackupPath(),
                $this->paths->getPackedPath()
            );
        }

        // Delete resources backup folder
        if ($this->filesystem->has($this->paths->getResourcesBackupPath())) {
            $this->filesystem->deleteDir($this->paths->getResourcesBackupPath());
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

    /**
     * Get all workshop mods currently downloaded.
     *
     * @return Mod[]|Collection
     */
    public function getMods(): Collection
    {
        $mods = $this->filesystem->listContents($this->paths->getModsPath());
        foreach ($mods as &$mod) {
            $mod = new Mod($mod);
        }

        return new Collection($mods);
    }

    /**
     * Get all mods that are graphical only.
     *
     * @return Mod[]|Collection
     */
    public function getGraphicalMods(): Collection
    {
        return $this->getMods()->filter(function (Mod $mod) {
            return !$this->filesystem->has($mod->getPath('main.lua')) && $this->filesystem->has($mod->getPath('resources'));
        });
    }
}
