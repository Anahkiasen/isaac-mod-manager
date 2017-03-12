<?php

namespace Isaac\Console;

use Exception;
use Humbug\SelfUpdate\Strategy\GithubStrategy;
use Humbug\SelfUpdate\Updater;
use Isaac\Application;

class SelfUpdate extends AbstractCommand
{
    /**
     * Fire the command.
     *
     * @return int|null|void
     */
    protected function fire()
    {
        $strategy = new GithubStrategy();
        $strategy->setPackageName('anahkiasen/isaac-mod-manager');
        $strategy->setPharName('imm.phar');
        $strategy->setCurrentLocalVersion(Application::VERSION);

        $updater = new Updater();
        $updater->setStrategyObject($strategy);

        try {
            $result = $updater->update();

            return $result ? $this->output->success('PHAR successfully updated!') : $this->output->comment('No update needed');
        } catch (Exception $exception) {
            $this->output->error('Error during update: '.$exception->getMessage());
        }
    }
}