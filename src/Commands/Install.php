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
            ->addArgument('mods', InputArgument::IS_ARRAY, 'The Steam ID of one or more mod(s) to install');
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        $modsQueue = $this->getModsQueue();

        // Rename packed folder if necessary
        if (!$this->mods->areResourcesBackup()) {
            $this->output->writeln('<comment>Making a backup of resources folder, this only has to be done once</comment>');
            $this->mods->backup();
        }

        // Present mods to install
        $this->output->title('Installing '.count($modsQueue).' mod(s):');
        $this->presentMods($modsQueue);
        $progress = new ProgressBar($this->output);
        $progress->setMessage('');
        $progress->setFormat(
            '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%'.PHP_EOL.PHP_EOL.
            'Installing <comment>%message%</comment>'
        );

        // Install mods
        $progress->start(count($modsQueue));
        foreach ($modsQueue as $mod) {
            $progress->setMessage($mod->getName());
            $this->mods->installMod($mod);
            $progress->advance();
        }

        $progress->finish();
        $this->output->success(count($modsQueue).' mod(s) installed successfully!');
    }
}
