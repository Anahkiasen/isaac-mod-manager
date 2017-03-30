<?php

namespace Isaac\Bus\Commands;

use Humbug\SelfUpdate\Updater;
use Isaac\Services\Cache\TaggableCacheInterface;
use Isaac\TestCase;
use Prophecy\Argument;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckUpdatesTest extends TestCase
{
    public function testCanCheckForUpdates()
    {
        $this->expectOutputString('foo');

        /** @var Updater $updater */
        $updater = $this->prophesize(Updater::class);
        $updater->hasUpdate()->willReturn(true);
        $updater->getNewVersion()->shouldBeCalled()->willReturn('5.0.0');

        /** @var TaggableCacheInterface $cache */
        $cache = $this->prophesize(TaggableCacheInterface::class);
        $cache->has('selfupdate')->willReturn(false);
        $cache->set('selfupdate', true)->shouldBeCalled();

        /** @var SymfonyStyle $output */
        $output = $this->prophesize(SymfonyStyle::class);
        $output->confirm(Argument::cetera())->willReturn(true);

        $this->checkForUpdates(function () {
            echo 'foo';
        });
    }

    public function testDoesntAskUserForUpdateIfAlreadySaidNo()
    {
        $this->cache->set('selfupdate', false);

        /** @var Updater $updater */
        $updater = $this->prophesize(Updater::class);
        $updater->hasUpdate()->shouldBeCalled();
        $updater->getNewVersion()->shouldNotBeCalled();

        $this->checkForUpdates();
    }

    public function testDoesntUpdateIfUserRefuses()
    {
        $this->expectOutputString('');

        /** @var Updater $updater */
        $updater = $this->prophesize(Updater::class);
        $updater->hasUpdate()->willReturn(true);
        $updater->getNewVersion()->shouldBeCalled()->willReturn('5.0.0');

        /** @var TaggableCacheInterface $cache */
        $cache = $this->prophesize(TaggableCacheInterface::class);
        $cache->has('selfupdate')->willReturn(false);
        $cache->set('selfupdate', false)->shouldBeCalled();

        /** @var SymfonyStyle $output */
        $output = $this->prophesize(SymfonyStyle::class);
        $output->confirm(Argument::cetera())->willReturn(false);

        $this->checkForUpdates(function () {
            echo 'foo';
        });
    }

    /**
     * @param callable|null $callback
     */
    protected function checkForUpdates(callable $callback = null)
    {
        $command = new CheckUpdates($callback);
        $command->setCheckVersion(false);

        $this->bus->handle($command);
    }
}