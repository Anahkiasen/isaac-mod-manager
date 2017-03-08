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
            ->addArgument('mods', InputArgument::IS_ARRAY, 'The Steam ID of one or more mod(s) to uninstall');
    }

    /**
     * Fire the command.
     *
     * @return int|null|void
     */
    protected function fire()
    {
        $modsQueue = $this->getModsQueue();

        $this->output->title('Uninstalling '.count($modsQueue).' mod(s)');
        $this->presentMods($modsQueue);

        $this->mods->removeMods($modsQueue);
        $this->output->success(count($modsQueue).' mod(s) uninstalled successfully!');
    }
}
