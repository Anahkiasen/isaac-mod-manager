<?php

namespace Isaac\Services;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\Process\Process;

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

    /**
     * Check whether the game is already unpacked or not.
     *
     * @return bool
     */
    public function isGameUnpacked(): bool
    {
        return !$this->filesystem->has($this->paths->getPackedPath()) && $this->filesystem->has($this->paths->getPackedBackupPath());
    }

    /**
     * Unpack the game's resources.
     */
    public function unpack()
    {
        // Unpack resources
        $process = new Process($this->paths->getGamePath().DS.'tools'.DS.'ResourceExtractor'.DS.'ResourceExtractor.exe');
        $process->run();

        // Rename "packed" folder
        $this->filesystem->rename(
            $this->paths->getPackedPath(),
            $this->paths->getPackedBackupPath()
        );
    }

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
     * @return array
     */
    public function getGraphicalMods(): array
    {
        return array_filter($this->getMods(), function (Mod $mod) {
            return !$this->filesystem->has($mod->getPath('main.lua')) && $this->filesystem->has($mod->getPath('resources'));
        });
    }
}