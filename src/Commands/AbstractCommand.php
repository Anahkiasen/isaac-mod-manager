<?php

namespace Isaac\Commands;

use Isaac\Bus\Commands\GatherPaths;
use Isaac\Services\Mods\ModsManager;
use League\Tactician\CommandBus;
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
     * @var CommandBus
     */
    private $bus;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var ModsManager
     */
    protected $mods;

    /**
     * @var bool
     */
    protected $needsSetup = false;

    /**
     * @param CommandBus     $bus
     * @param CacheInterface $cache
     * @param ModsManager    $mods
     */
    public function __construct(CommandBus $bus, CacheInterface $cache, ModsManager $mods)
    {
        $this->bus = $bus;
        $this->cache = $cache;
        $this->mods = $mods;

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
        $this->bus->handle(new GatherPaths($this->output));

        // Ensure resources are extracted
        if (!$this->mods->areResourcesExtracted() && $this->getName() !== 'restore') {
            throw new RuntimeException('You must first run the ResourceExtractor in /tools/ResourceExtractor/ResourceExtractor.exe');
        }
    }
}
