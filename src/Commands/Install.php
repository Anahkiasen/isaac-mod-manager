<?php

namespace Isaac\Commands;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Copies non-LUA mods into your resource folder.
 */
class Install extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        return $this
            ->setName('mods:install')
            ->setDescription('Copies non-LUA mods into your resource folder')
            ->setNeedsSetup(true)
            ->addArgument('mod', InputArgument::OPTIONAL, 'The Steam ID of a mod to install');
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        // Get all mods that are only graphical
        $mod = $this->input->getArgument('mod');
        $modsQueue = $mod ? [$this->mods->findModById($mod)] : $this->mods->getGraphicalMods();

        // Rename packed folder if necessary
        if (!$this->mods->areResourcesBackup()) {
            $this->output->writeln('<comment>Making a backup of resources folder, this only has to be done once</comment>');
            $this->mods->backup();
        }

        // Install mods
        $this->output->title('Installing '.count($modsQueue).' mods');
        $progress = new ProgressBar($this->output);
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%'.PHP_EOL.'%message%');
        $progress->setMessage('');
        $progress->start(count($modsQueue));
        foreach ($modsQueue as $mod) {
            $progress->setMessage($mod->getName());
            $this->mods->installMod($mod);
            $progress->advance();
        }

        $progress->finish();
        $this->output->success(count($modsQueue).' mods installed successfully!');
    }
}
