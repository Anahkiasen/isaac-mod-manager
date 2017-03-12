<?php

namespace Isaac\Console\Commands;

class Restore extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        return $this
            ->setName('restore')
            ->setDescription('Restores Isaac to its original state')
            ->setNeedsSetup(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        $this->bus->handle(new \Isaac\Bus\Commands\Restore());
        $this->output->success('Isaac restored to its original state');
    }
}
