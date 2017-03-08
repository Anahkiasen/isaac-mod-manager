<?php

namespace Isaac\Commands;

class Restore extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        return $this
            ->setName('restore')
            ->setDescription('Restores Isaac to its original state');
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        $this->mods->restore();
        $this->output->success('Isaac restored to its original state');
    }
}
