<?php

namespace Isaac\Bus\Commands;

use Isaac\Services\Mods\ModsManager;
use Isaac\Services\Pathfinder;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Backup
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param ModsManager         $mods
     * @param FilesystemInterface $files
     * @param Pathfinder          $paths
     */
    public function handle(ModsManager $mods, FilesystemInterface $files, Pathfinder $paths)
    {
        if ($mods->areResourcesBackup()) {
            return;
        }

        $this->output->writeln('<comment>Making a backup of resources folder, this only has to be done once</comment>');
        $this->output->writeln('It can take a few minutes so be patient');

        if (!$files->has($paths->getResourcesBackupPath())) {
            $files->copyDirectory($paths->getResourcesPath(), $paths->getResourcesBackupPath());
        }

        if (!$files->has($paths->getPackedBackupPath())) {
            $files->rename($paths->getPackedPath(), $paths->getPackedBackupPath());
        }
    }
}
