<?php

namespace Isaac\Commands;

use Isaac\Services\ModsManager;

/**
 * Removes all installed mods.
 */
class Uninstall extends AbstractCommand
{
    /**
     * @var ModsManager
     */
    protected $manager;

    /**
     * @param ModsManager $manager
     */
    public function __construct(ModsManager $manager)
    {
        $this->manager = $manager;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        return $this
            ->setName('uninstall')
            ->setDescription('Removes all installed mods.');
    }

    /**
     * Fire the command.
     *
     * @return int|null|void
     */
    protected function fire()
    {
        $this->manager->repack();
        $this->output->success('Game successfully repacked');
    }
}
