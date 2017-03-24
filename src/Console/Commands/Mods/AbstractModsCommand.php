<?php

namespace Isaac\Console\Commands\Mods;

use Humbug\SelfUpdate\Updater;
use Illuminate\Support\Collection;
use Isaac\Console\Commands\AbstractCommand;
use Isaac\Console\ModsChoice;
use Isaac\Services\Conflicts\ConflictsHandler;
use Isaac\Services\Mods\Mod;
use Isaac\Services\Mods\ModNotFoundException;
use Isaac\Services\Mods\ModsManager;
use League\Tactician\CommandBus;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractModsCommand extends AbstractCommand
{
    /**
     * @var ConflictsHandler
     */
    protected $conflicts;

    /**
     * {@inheritdoc}
     */
    public function __construct(CommandBus $bus, CacheInterface $cache, ModsManager $mods, ConflictsHandler $conflicts, Updater $updater)
    {
        $this->conflicts = $conflicts;

        parent::__construct($bus, $cache, $mods, $updater);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        return $this
            ->addArgument('mods', InputArgument::IS_ARRAY, 'The Steam ID of one or more mod(s) to uninstall')
            ->addOption('graphical', 'g', InputOption::VALUE_NONE, 'Only graphical mods')
            ->addOption('select', 's', InputOption::VALUE_NONE, 'Select which mods to install from a picklist')
            ->setNeedsSetup(true);
    }

    /**
     * @return Mod[]|Collection
     */
    protected function getModsQueue(): Collection
    {
        // Get the mods queue
        $mods = $this->input->getArgument('mods');
        $fallback = $this->input->getOption('graphical') ? 'getGraphicalMods' : 'getMods';
        $modsQueue = $mods ? $this->mods->findMods($mods) : $this->mods->$fallback();

        // Check if the user wants a picklist
        if ($this->input->getOption('select')) {
            $question = new ModsChoice('Select which mods to install', $modsQueue);
            $modsQueue = $question->getModsFromAnswer($this->output->askQuestion($question));
        }

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
    protected function presentMods(string $action, Collection $modsQueue)
    {
        $this->output->title(sprintf('%s %d mod(s)', $action, $modsQueue->count()));
        $this->output->listing(
            $modsQueue->map->getName()->all()
        );
    }
}
