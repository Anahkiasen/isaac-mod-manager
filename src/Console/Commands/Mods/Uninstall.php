<?php

namespace Isaac\Console\Commands\Mods;

/**
 * Removes all installed mods.
 */
class Uninstall extends AbstractModsCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        return parent::configure()
            ->setName('mods:uninstall')
            ->setDescription('Removes all installed mods.');
    }

    /**
     * Fire the command.
     *
     * @return int|null|void
     */
    protected function fire()
    {
        $modsQueue = $this->getModsQueue();

        $this->presentMods('Uninstalling', $modsQueue);
        $this->mods->removeMods($modsQueue);

        $this->output->success(count($modsQueue).' mod(s) uninstalled successfully!');
    }
}
