<?php

namespace Isaac\Bus\Commands;

use Isaac\Services\Environment\Pathfinder;
use Isaac\Services\Mods\ModsManager;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Backs up the resources/ and packed/ folders for later retrieval.
 */
class Backup
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output = null)
    {
        $this->output = $output ?: new NullOutput();
    }

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
