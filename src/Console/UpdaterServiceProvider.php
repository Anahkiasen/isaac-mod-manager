<?php

namespace Isaac\Console;

use Humbug\SelfUpdate\Strategy\GithubStrategy;
use Humbug\SelfUpdate\Updater;
use Isaac\Application;
use League\Container\ServiceProvider\AbstractServiceProvider;

class UpdaterServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [Updater::class];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->share(Updater::class, function() {
            $version = explode('-', Application::VERSION)[0];

            // Create Github strategy for PHAR updates
            $strategy = new GithubStrategy();
            $strategy->setPackageName('anahkiasen/isaac-mod-manager');
            $strategy->setPharName('imm.phar');
            $strategy->setCurrentLocalVersion($version);

            // Create Updater without pubkey signing
            $updater = new Updater(null, false);
            $updater->setStrategyObject($strategy);
            $updater->hasUpdate();

            return $updater;
        });
    }
}