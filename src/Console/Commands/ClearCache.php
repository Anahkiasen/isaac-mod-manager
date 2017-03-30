<?php

namespace Isaac\Console\Commands;

use Isaac\Services\Conflicts\ConflictsHandler;
use Symfony\Component\Console\Input\InputOption;

class ClearCache extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        return $this
            ->setName('cache:clear')
            ->setDescription('Clear the paths cache to your game and mods')
            ->addOption('conflicts', 'c', InputOption::VALUE_NONE, 'Only clear the conflicts resolution cache');
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        if ($this->input->getOption('conflicts')) {
            $this->cache->invalidateTag(ConflictsHandler::CACHE_TAG);
        } else {
            $this->cache->clear();
        }

        $this->output->success('Cache cleared successfully');
    }
}
