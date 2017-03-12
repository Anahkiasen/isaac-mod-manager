<?php

namespace Isaac\Console\Commands;

use DateInterval;
use Humbug\SelfUpdate\Updater;
use Isaac\Bus\Commands\ExtractResources;
use Isaac\Bus\Commands\GatherPaths;
use Isaac\Services\Mods\ModsManager;
use League\Tactician\CommandBus;
use Psr\SimpleCache\CacheInterface;
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
     * @var CommandBus
     */
    protected $bus;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var ModsManager
     */
    protected $mods;

    /**
     * @var Updater
     */
    protected $updater;

    /**
     * @var bool
     */
    protected $needsSetup = false;

    /**
     * @param CommandBus     $bus
     * @param CacheInterface $cache
     * @param ModsManager    $mods
     * @param Updater        $updater
     */
    public function __construct(CommandBus $bus, CacheInterface $cache, ModsManager $mods, Updater $updater)
    {
        $this->bus = $bus;
        $this->cache = $cache;
        $this->mods = $mods;
        $this->updater = $updater;

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

        // Check for new version
        $this->checkUpdates();

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
     * Check for potential updates.
     */
    protected function checkUpdates(): void
    {
        if ($version = $this->updater->hasUpdate()) {
            $this->output->note('A new version is available: '.$version.PHP_EOL.'Run imm self-update to update');
        }
    }

    /**
     * Setup the CLI application with the necessary informations.
     */
    protected function setup()
    {
        $this->bus->handle(new GatherPaths($this->output));
        $this->bus->handle(new ExtractResources(
            $this->output,
            $this->getHelper('process')
        ));
    }
}
