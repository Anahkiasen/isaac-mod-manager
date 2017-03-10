<?php

namespace Isaac\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

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
            ->addArgument('mods', InputArgument::IS_ARRAY, 'The Steam ID of one or more mod(s) to uninstall')
            ->addOption('graphical', 'G', InputOption::VALUE_NONE, 'Only graphical mods');
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
