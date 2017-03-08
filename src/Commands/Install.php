<?php

namespace Isaac\Commands;

use Isaac\Services\Pathfinder;
use League\Flysystem\FilesystemInterface;

class Install extends AbstractCommand
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

        parent::__construct();
    }

    /**
     * @return $this
     */
    protected function configure()
    {
        return $this
            ->setName('install')
            ->setDescription('Copies non-LUA mods into your resource folder');
    }

    /**
     * {@inheritdoc}
     */
    public function fire()
    {
        // Get all mods that are only graphical
        $workshopMods = $this->filesystem->listContents($this->paths->getModsFolder());
        $workshopMods = array_filter($workshopMods, function ($mod) {
            return !$this->filesystem->has($mod['path'].'/main.lua') && $this->filesystem->has($mod['path'].'/resources');
        });

        // Rename packed folder if necessary
        if ($this->filesystem->has($this->paths->getPackedFolder())) {
            $this->output->writeln('<comment>A "packed" folde found, renaming</comment>');
            $this->filesystem->rename($this->paths->getPackedFolder(), $this->paths->getPackedFolderBackup());
        }

        // Install mods
        $this->output->title('Installing '.count($workshopMods).' mods');
        $this->output->progressStart(count($workshopMods));
        foreach ($workshopMods as $mod) {
            $resourcesPath = $mod['path'].'/resources';
            foreach ($this->filesystem->listFiles($resourcesPath, true) as $file) {
                $relativePath = str_replace($resourcesPath, null, $file['path']);
                $this->filesystem->forceCopy($file['path'], $this->paths->getGameFolder().$relativePath);
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->output->success('Mods installed successfully!');
    }
}
