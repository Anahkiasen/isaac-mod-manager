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
        return $this->filesystem->has($this->paths->getResourcesBackupPath()) && $this->filesystem->has($this->paths->getPackedBackupPath());
    }

    /**
     * @return bool
     */
    public function areResourcesExtracted(): bool
    {
        return $this->filesystem->has($this->paths->getResourcesPath().DS.'achievements.xml');
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
        foreach ($mod->listFiles() as $file) {
            $original = $this->paths->getModeFileInResourcesBackup($mod, $file['path']);
            $destination = $this->paths->getModeFileInResources($mod, $file['path']);

            if ($this->filesystem->has($original)) {
                $this->filesystem->forceCopy($original, $destination);
            } elseif ($this->filesystem->has($destination)) {
                $this->filesystem->delete($destination);
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
     *
     * @TODO: Test for bug when main lua file not in backup
     */
    public function restoreMainLua()
    {
        $originalLua = str_replace($this->paths->getResourcesPath(), $this->paths->getResourcesBackupPath(), $this->paths->getMainLuaPath());
        if (!$this->filesystem->has($originalLua)) {
            $this->filesystem->forceCopy($originalLua, $this->paths->getMainLuaPath());
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
            $mod = new Mod($mod['path']);
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
