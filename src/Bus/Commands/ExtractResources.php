<?php

namespace Isaac\Bus\Commands;

use Isaac\Services\Mods\ModsManager;
use Isaac\Services\Pathfinder;
use RuntimeException;

class ExtractResources
{
    /**
     * @param ModsManager $mods
     * @param Pathfinder  $paths
     */
    public function handle(ModsManager $mods, Pathfinder $paths)
    {
        if ($mods->areResourcesExtracted()) {
            return;
        }

        throw new RuntimeException('You must first run the ResourceExtractor in '.$paths->getResourceExtractorPath());
    }
}