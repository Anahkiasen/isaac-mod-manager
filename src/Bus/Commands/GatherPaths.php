<?php

namespace Isaac\Bus\Commands;

use Isaac\Services\Pathfinder;
use League\Flysystem\FilesystemInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Checks the existence of the required paths in cache,
 * and asks the user for them otherwise.
 */
class GatherPaths
{
    /**
     * @var SymfonyStyle
     */
    protected $output;

    /**
     * @param SymfonyStyle $output
     */
    public function __construct(SymfonyStyle $output)
    {
        $this->output = $output;
    }

    /**
     * @param CacheInterface      $cache
     * @param FilesystemInterface $files
     * @param Pathfinder          $paths
     */
    public function handle(CacheInterface $cache, FilesystemInterface $files, Pathfinder $paths)
    {
        if ($cache->has('source') && $cache->has('destination')) {
            return;
        }

        // Gather paths to folders
        $this->output->title('Before we begin I need some informations from you!');
        $destination = $this->output->ask('Where is Afterbirth+ installed?', $paths->getGamePath());

        // Try to infer path to mods
        if ($files->has($paths->getSavedataPath())) {
            // ...
        }

        $source = $this->output->ask('Where are your Afterbirth+ Workshop mods located?', $paths->getModsPath());

        // Cache for later use
        $cache->set('source', $source);
        $cache->set('destination', $destination);

        $this->output->success('Setup completed, all good!');
    }
}