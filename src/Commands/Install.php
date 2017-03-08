<?php

namespace Isaac\Commands;

use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Copies non-LUA mods into your resource folder.
 */
class Install extends AbstractCommand
{
    /**
     * @return $this
     */
    protected function configure()
    {
        return $this
            ->setName('install')
            ->setDescription('Copies non-LUA mods into your resource folder');
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        // Get all mods that are only graphical
        $graphicalMods = $this->mods->getGraphicalMods();

        // Rename packed folder if necessary
        if (!$this->mods->areResourcesBackup()) {
            $this->output->writeln('<comment>Making a backup of resources folder, this only has to be done once</comment>');
            $this->mods->backup();
        }

        // Install mods
        $this->output->title('Installing '.count($graphicalMods).' mods');
        $progress = new ProgressBar($this->output);
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%'.PHP_EOL.'%message%');
        $progress->setMessage('');
        $progress->start(count($graphicalMods));
        foreach ($graphicalMods as $mod) {
            $progress->setMessage($mod->getName());
            $this->mods->installMod($mod);
            $progress->advance();
        }

        $progress->finish();
        $this->output->success(count($graphicalMods).' mods installed successfully!');
    }
}
