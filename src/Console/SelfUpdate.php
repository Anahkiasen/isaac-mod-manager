<?php

namespace Isaac\Console;

use Exception;
use Isaac\Application;

class SelfUpdate extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        return $this
            ->setName('self-update')
            ->setDescription('Updates IMM to the latest stable version');
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        try {
            if ($this->updater->update()) {
                $this->output->success('PHAR successfully updated!');
            } else {
                $this->output->success('No update needed! '.Application::VERSION.' is the latest version.');
            }
        } catch (Exception $exception) {
            $this->output->error('Error during update: '.$exception->getMessage());
        }
    }
}
