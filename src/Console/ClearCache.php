<?php

namespace Isaac\Console;

class ClearCache extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        return $this
            ->setName('cache:clear')
            ->setDescription('Clear the paths cache to your game and mods');
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        $this->cache->clear();
        $this->output->success('Cache cleared successfully');
    }
}
