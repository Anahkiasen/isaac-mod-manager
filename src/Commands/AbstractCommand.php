<?php

namespace Isaac\Commands;

use Illuminate\Support\Collection;
use Isaac\Services\Conflicts\ConflictsHandler;
use Isaac\Services\Mods\Mod;
use Isaac\Services\Mods\ModCollection;
use Isaac\Services\Mods\ModNotFoundException;
use Isaac\Services\Mods\ModsManager;
use Psr\SimpleCache\CacheInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * A container-aware command that wraps output in Symfony style.
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var SymfonyStyle
     */
    protected $output;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var ModsManager
     */
    protected $mods;

    /**
     * @var ConflictsHandler
     */
    protected $conflicts;

    /**
     * @var bool
     */
    protected $needsSetup = false;

    /**
     * @param CacheInterface                   $cache
     * @param \Isaac\Services\Mods\ModsManager $mods
     * @param ConflictsHandler                 $conflicts
     */
    public function __construct(CacheInterface $cache, ModsManager $mods, ConflictsHandler $conflicts)
    {
        $this->cache = $cache;
        $this->mods = $mods;
        $this->conflicts = $conflicts;

        parent::__construct();
    }

    /**
     * @param bool $needsSetup
     *
     * @return self
     */
    public function setNeedsSetup(bool $needsSetup): self
    {
        $this->needsSetup = $needsSetup;

        return $this;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = new SymfonyStyle($input, $output);

        // Preliminary setup
        if ($this->needsSetup) {
            $this->setup();
        }

        return $this->fire();
    }

    /**
     * Fire the command.
     *
     * @return int|null|void
     */
    abstract protected function fire();

    ////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////// MODS //////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * Setup the CLI application with the necessary informations.
     */
    protected function setup()
    {
        if (!$this->cache->has('source') || !$this->cache->has('destination')) {
            // Gather paths to folders
            $this->output->title('Before we begin I need some informations from you!');
            $source = $this->output->ask('Where are your Afterbirth+ Workshop mods located?', 'C:/Users/YourName/Documents/My Games/Binding of Isaac Afterbirth+ Mods');
            $destination = $this->output->ask('Where is Afterbirth+ installed?', 'C:/Program Files (x86)/Steam/steamapps/common/The Binding of Isaac Rebirth');

            // Cache for later use
            $this->cache->set('source', $source);
            $this->cache->set('destination', $destination);

            $this->output->success('Setup completed, all good!');
        }

        // Ensure resources are extracted
        if (!$this->mods->areResourcesExtracted() && $this->getName() !== 'restore') {
            throw new RuntimeException('You must first run the ResourceExtractor in /tools/ResourceExtractor/ResourceExtractor.exe');
        }
    }

    /**
     * @return Mod[]|ModCollection
     */
    protected function getModsQueue(): ModCollection
    {
        $mods = $this->input->getArgument('mods');
        $fallback = $this->input->getOption('graphical') ? 'getGraphicalMods' : 'getMods';
        $modsQueue = $mods ? $this->mods->findMods($mods) : $this->mods->$fallback();
        if ($modsQueue->isEmpty()) {
            throw new ModNotFoundException($mods);
        }

        return $modsQueue;
    }

    /**
     * Presents a collection of mods as a listing.
     *
     * @param string     $action
     * @param Collection $modsQueue
     */
    protected function presentMods(string $action, Collection $modsQueue): void
    {
        $this->output->title(sprintf('%s %d mod(s)', $action, $modsQueue->count()));
        $this->output->listing(
            $modsQueue->map->getName()->all()
        );
    }
}
