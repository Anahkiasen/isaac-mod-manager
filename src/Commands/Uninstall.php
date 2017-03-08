<?php

namespace Isaac\Commands;

/**
 * Removes all installed mods.
 */
class Uninstall extends AbstractCommand
{
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
        $this->mods->removeMods();
        $this->output->success('Game successfully repacked');
    }
}
