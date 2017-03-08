<?php

namespace Isaac\Commands;

use Isaac\Services\Mods\ModsManager;
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
     * @param CacheInterface                   $cache
     * @param \Isaac\Services\Mods\ModsManager $mods
     */
    public function __construct(CacheInterface $cache, ModsManager $mods)
    {
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

    /**
     * Setup the CLI application with the necessary informations.
     */
    protected function setup()
    {
        if ($this->cache->has('source') && $this->cache->has('destination') && $this->mods->areResourcesBackup()) {
            return;
        }

        // Gather paths to folders
        $this->output->title('Before we begin I need some informations from you!');
        $source = $this->output->ask('Where are your Afterbirth+ Workshop mods located?', 'C:/Users/YourName/Documents/My Games/Binding of Isaac Afterbirth+ Mods');
        $destination = $this->output->ask('Where is Afterbirth+ installed?', 'C:/Program Files (x86)/Steam/steamapps/common/The Binding of Isaac Rebirth');

        // Cache for later use
        $this->cache->set('source', $source);
        $this->cache->set('destination', $destination);

        if (!$this->mods->areResourcesBackup() && $this->getName() !== 'restore') {
            return $this->output->error('You must first run the ResourceExtractor in /tools/ResourceExtractor/ResourceExtractor.exe');
        }

        $this->output->success('Setup completed, all good!');
    }
}
