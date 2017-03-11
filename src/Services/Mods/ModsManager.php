<?php

namespace Isaac\Services\Mods;

use Illuminate\Support\Collection;
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
     * @param int[]|string[] $mods
     *
     * @return Mod[]|Collection
     */
    public function findMods(array $mods): Collection
    {
        return Collection::make($mods)
            ->unique()
            ->map(function ($modId) {
                $isName = (int) $modId === 0;

                try {
                    return $isName ? $this->findModByName($modId) : $this->findModById($modId);
                } catch (ModNotFoundException $exception) {
                    return;
                }
            })
            ->filter();
    }

    /**
     * Find a mod with a given ID.
     *
     * @param int $modId
     *
     * @return Mod
     */
    public function findModById(int $modId): Mod
    {
        if ($first = $this->getMods()->first->isMod($modId)) {
            return $first;
        }

        throw new ModNotFoundException($modId);
    }

    /**
     * Find a mod that matches a given name.
     *
     * @param string $name
     *
     * @return Mod
     */
    public function findModByName(string $name): Mod
    {
        if ($first = $this->getMods()->first->isNamed($name)) {
            return $first;
        }

        throw new ModNotFoundException($name);
    }

    /**
     * Install a given mod.
     *
     * @param Mod $mod
     */
    public function installMod(Mod $mod)
    {
        foreach ($mod->listFiles() as $file) {
            $filepath = $file['path'];
            switch ($file['basename']) {
                case 'main.lua':
                    $contents = $this->filesystem->read($this->paths->getMainLuaPath());
                    $contents .= PHP_EOL.$this->filesystem->read($filepath);

                    $this->filesystem->put($this->paths->getMainLuaPath(), $contents);
                    break;

                default:
                    $this->filesystem->forceCopy($filepath, $this->paths->getModeFileInResources($mod, $filepath));
                    break;
            }
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
            $original = $this->paths->getModeFileInResourcesBackup($mod, $file['path']);
            $destination = $this->paths->getModeFileInResources($mod, $file['path']);

            if ($this->filesystem->has($original)) {
                $this->filesystem->forceCopy($original, $destination);
            }
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
     * Restores the main.lua file for further modification.
     */
    public function restoreMainLua()
    {
        $this->filesystem->forceCopy(
            str_replace($this->paths->getResourcesPath(), $this->paths->getResourcesBackupPath(), $this->paths->getMainLuaPath()),
            $this->paths->getMainLuaPath()
        );
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
            $mod->setFilesystem($this->filesystem);
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
        return $this->getMods()->filter->isGraphical();
    }

    /**
     * Get all mods that have LUA coding.
     *
     * @return Mod[]|Collection
     */
    public function getLuaMods(): Collection
    {
        return $this->getMods()->reject->isGraphical();
    }
}
