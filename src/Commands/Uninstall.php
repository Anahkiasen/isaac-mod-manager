<?php

namespace Isaac\Commands;

use Isaac\Services\ModsManager;

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
            ->setDescription('Uninstall all mods and restore the game to its original state');
    }

    /**
     * Fire the command.
     *
     * @return int|null|void
     */
    public function fire()
    {
        $this->manager->repack();
        $this->output->success('Game successfully repacked');
    }
}