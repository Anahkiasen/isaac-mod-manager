<?php

namespace Isaac\Console\Commands\Mods;

use Isaac\Bus\Commands\Backup;
use Isaac\Console\ModsChoice;
use Isaac\Services\Conflicts\Conflict;

/**
 * Copies non-LUA mods into your resource folder.
 */
class Install extends AbstractModsCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        return parent::configure()
            ->setName('mods:install')
            ->setDescription('Copies non-LUA mods into your resource folder');
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        $modsQueue = $this->getModsQueue();

        // Rename packed folder if necessary
        $this->bus->handle(new Backup());

        // Resolve eventual conflicts in the mods
        $modsQueue = $this->conflicts->findAndResolve($modsQueue, function (Conflict $conflict) {
            $isMultiple = $conflict->canHaveMultipleResolutions();

            $this->output->section('<fg=red>Found conflicts for:</fg=red> '.$conflict->getPath());
            $this->output->note('Press ENTER to skip and let the mods overwrite each other.');
            if ($isMultiple) {
                $this->output->caution('Note: Checking multiple can have unforeseen consequences');
            }

            // Ask user to select which mods to use
            $question = new ModsChoice('Which mod(s) would you like to use here?', $conflict, $isMultiple);
            $modIds = $question->getModIdsFromAnswer(
                $this->output->askQuestion($question)
            );

            return $modIds;
        });

        // Present mods to install
        $this->presentMods('Installing', $modsQueue);
        $progress = $this->output->createProgressBar($modsQueue->count());
        $progress->setMessage('');
        $progress->setFormat(
            '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%'.PHP_EOL.PHP_EOL.'Installing <comment>%message%</comment>'
        );

        // Restore main.lua file
        $this->mods->restoreMainLua();

        // Install mods
        $progress->start();
        foreach ($modsQueue as $mod) {
            $progress->setMessage($mod->getName());
            $this->mods->installMod($mod);
            $progress->advance();
        }

        $progress->finish();
        $this->output->success($modsQueue->count().' mod(s) installed successfully!');
    }
}
