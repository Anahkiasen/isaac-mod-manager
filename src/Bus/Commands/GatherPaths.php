<?php

namespace Isaac\Bus\Commands;

use Isaac\Bus\OutputAwareInterface;
use Isaac\Bus\OutputAwareTrait;
use Isaac\Services\Environment\Pathfinder;
use League\Flysystem\FilesystemInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Checks the existence of the required paths in cache,
 * and asks the user for them otherwise.
 */
class GatherPaths implements OutputAwareInterface
{
    use OutputAwareTrait;

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
        $this->getOutput()->title('Before we begin I need some informations from you!');
        $destination = $this->getOutput()->ask('Where is Afterbirth+ installed?', $paths->getGamePath());

        // Try to infer path to mods
        $modsPath = $paths->getModsPath();
        if ($files->has($paths->getSavedataPath())) {
            $contents = $files->read($paths->getSavedataPath());
            preg_match_all('/Modding Data Path: ([^\r]+)/', $contents, $matches);
            if (isset($matches[1][0]) && $files->has($matches[1][0])) {
                $modsPath = $matches[1][0];
            }
        }

        $source = $this->getOutput()->ask('Where are your Afterbirth+ Workshop mods located?', $modsPath);

        // Cache for later use
        $cache->set('source', $source);
        $cache->set('destination', $destination);

        $this->getOutput()->success('Setup completed, all good!');
    }
}
