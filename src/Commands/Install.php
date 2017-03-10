<?php

namespace Isaac\Commands;

use Isaac\Services\Conflicts\Conflict;
use Isaac\Services\Mods\Mod;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ChoiceQuestion;

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
            ->addArgument('mods', InputArgument::IS_ARRAY, 'The Steam ID of one or more mod(s) to install')
            ->addOption('graphical', 'G', InputOption::VALUE_NONE, 'Only graphical mods');
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

            // Compute choices
            $choices = $conflict->map(function (Mod $mod) {
                return sprintf('%s (%s)', $mod->getName(), $mod->getId());
            });

            $resolutions = $conflict->mapWithKeys(function (Mod $mod) {
                return [$mod->getName() => $mod->getId()];
            });

            // Ask user to select which mods to use
            $question = new ChoiceQuestion('Which mod(s) would you like to use here?', $choices->all());
            $question->setMultiselect(true);
            $question->setValidator();
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
