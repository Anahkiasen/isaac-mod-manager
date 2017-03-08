<?php

namespace Isaac\Commands;

use Symfony\Component\Console\Input\InputArgument;

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
            ->setName('mods:uninstall')
            ->setDescription('Removes all installed mods.')
            ->setNeedsSetup(true)
            ->addArgument('mod', InputArgument::OPTIONAL, 'The Steam ID of a mod to uninstall');
    }

    /**
     * Fire the command.
     *
     * @return int|null|void
     */
    protected function fire()
    {
        $modsQueue = $this->getModsQueue();

        $this->mods->removeMods($modsQueue);
        $this->output->success(count($modsQueue).' mod(s) uninstalled successfully!');
    }
}
