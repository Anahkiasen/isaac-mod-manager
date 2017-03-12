<?php

namespace Isaac\Bus\Commands;

use Isaac\Bus\OutputAwareInterface;
use Isaac\Bus\OutputAwareTrait;
use Isaac\Services\Environment\Environment;
use Isaac\Services\Environment\Pathfinder;
use Isaac\Services\Mods\ModsManager;
use RuntimeException;
use Symfony\Component\Console\Helper\ProcessHelper;

/**
 * Extracts the packed resources into workable files.
 */
class ExtractResources implements OutputAwareInterface
{
    use OutputAwareTrait;

    /**
     * @var ProcessHelper
     */
    protected $processes;

    /**
     * @param ProcessHelper $helper
     */
    public function __construct(ProcessHelper $helper)
    {
        $this->processes = $helper;
    }

    /**
     * @param ModsManager $mods
     * @param Pathfinder  $paths
     *
     * @return \Symfony\Component\Process\Process|void
     */
    public function handle(ModsManager $mods, Pathfinder $paths)
    {
        if ($mods->areResourcesExtracted()) {
            return;
        }

        if (Environment::isUnix()) {
            $this->getOutput()->writeln('<comment>Extracting resources</comment>');
            $target = $paths->getResourcesPath().DS.'..';

            return $this->processes->run($this->getOutput(), [
                $paths->getResourceExtractorPath(),
                $target,
                $target,
            ]);
        }

        throw new RuntimeException('You must first run the ResourceExtractor in '.$paths->getResourceExtractorPath());
    }
}
