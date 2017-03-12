<?php

namespace Isaac\Console;

use Exception;
use Humbug\SelfUpdate\Strategy\GithubStrategy;
use Humbug\SelfUpdate\Updater;
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
        $strategy = new GithubStrategy();
        $strategy->setPackageName('anahkiasen/isaac-mod-manager');
        $strategy->setPharName('imm.phar');
        $strategy->setCurrentLocalVersion(Application::VERSION);

        $updater = new Updater(null, false);
        $updater->setStrategyObject($strategy);

        try {
            if ($updater->update()) {
                $this->output->success('PHAR successfully updated!');
            } else {
                $this->output->comment('No update needed');
            }
        } catch (Exception $exception) {
            $this->output->error('Error during update: '.$exception->getMessage());
        }
    }
}
