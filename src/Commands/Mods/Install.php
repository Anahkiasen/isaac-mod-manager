<?php

namespace Isaac\Commands\Mods;

use Isaac\Services\Conflicts\Conflict;
use Symfony\Component\Console\Question\ChoiceQuestion;

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
        if (!$this->mods->areResourcesBackup()) {
            $this->output->writeln('<comment>Making a backup of resources folder, this only has to be done once</comment>');
            $this->mods->backup();
        }

        // Resolve eventual conflicts in the mods
        $modsQueue = $this->conflicts->findAndResolve($modsQueue, function (Conflict $conflict) {
            $this->output->writeln('<fg=red>Found conflicts for:</fg=red> '.$conflict->getPath());
            $this->output->caution('Note: Checking multiple can have unforeseen consequences');

            // Compute choices and resolutions
            $choices = $conflict->getPossibleChoices();
            $resolutions = $conflict->getPossibleResolutions();

            // Ask user to select which mods to use
            $question = new ChoiceQuestion('Which mod(s) would you like to use here?', $choices->all());
            $question->setMultiselect(true);
            $question->setAutocompleterValues($choices->keys());

            // Retrieve mod IDs from selection
            $resolution = (array) $this->output->askQuestion($question);
            foreach ($resolution as &$choice) {
                $choice = $resolutions->get($choice);
            }

            return $resolution;
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