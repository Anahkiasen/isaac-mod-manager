<?php

namespace Isaac\Console\Mods;

use Illuminate\Support\Collection;
use Isaac\Console\AbstractCommand;
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
    public function __construct(CommandBus $bus, CacheInterface $cache, ModsManager $mods, ConflictsHandler $conflicts)
    {
        $this->conflicts = $conflicts;

        parent::__construct($bus, $cache, $mods);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        return $this
            ->addArgument('mods', InputArgument::IS_ARRAY, 'The Steam ID of one or more mod(s) to uninstall')
            ->addOption('graphical', 'g', InputOption::VALUE_NONE, 'Only graphical mods')
            ->setNeedsSetup(true);
    }

    /**
     * @return Mod[]|Collection
     */
    protected function getModsQueue(): Collection
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
