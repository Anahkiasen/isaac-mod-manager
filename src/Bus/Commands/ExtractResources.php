<?php

namespace Isaac\Bus\Commands;

use Isaac\Services\Mods\ModsManager;
use Isaac\Services\Pathfinder;
use RuntimeException;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Output\OutputInterface;

class ExtractResources
{
    /**
     * @var ProcessHelper
     */
    protected $processes;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param OutputInterface $output
     * @param ProcessHelper   $helper
     */
    public function __construct(OutputInterface $output, ProcessHelper $helper)
    {
        $this->processes = $helper;
        $this->output = $output;
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

        if ($paths->isUnix()) {
            $this->output->writeln('<comment>Extracting resources</comment>');
            $target = $paths->getResourcesPath().DS.'..';

            return $this->processes->run($this->output, [
                $paths->getResourceExtractorPath(),
                $target,
                $target,
            ]);
        }

        throw new RuntimeException('You must first run the ResourceExtractor in '.$paths->getResourceExtractorPath());
    }
}
