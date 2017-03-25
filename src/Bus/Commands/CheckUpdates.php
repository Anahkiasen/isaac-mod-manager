<?php

namespace Isaac\Bus\Commands;

use Humbug\SelfUpdate\Updater;
use Isaac\Application;
use Isaac\Bus\OutputAwareInterface;
use Isaac\Bus\OutputAwareTrait;
use Psr\SimpleCache\CacheInterface;

/**
 * Checks for updates and triggers a callback
 * if necessary.
 */
class CheckUpdates implements OutputAwareInterface
{
    use OutputAwareTrait;

    /**
     * @var callable|null
     */
    protected $callback;

    /**
     * @var bool
     */
    protected $checkVersion = true;

    /**
     * @param callable|null $callback
     */
    public function __construct(callable $callback = null)
    {
        $this->callback = $callback;
    }

    /**
     * @param bool $checkVersion
     */
    public function setCheckVersion(bool $checkVersion)
    {
        $this->checkVersion = $checkVersion;
    }

    /**
     * @param Updater        $updater
     * @param CacheInterface $cache
     */
    public function handle(Updater $updater, CacheInterface $cache)
    {
        $callback = $this->callback;

        // Only try to update if a) we're running the PHAR, b) there is an update c) the user wants updates
        $shouldUpdate = !$this->checkVersion || !Application::isDevelopmentVersion();
        $wantsUpdates = !$cache->has('selfupdate') || $cache->get('selfupdate');
        if (!$updater->hasUpdate() || !$shouldUpdate || !$wantsUpdates) {
            return;
        }

        // Print new version and changelog
        $version = $updater->getNewVersion();
        $question = sprintf(
            "A new version is available: <comment>%s</comment>, view changes at <comment>https://github.com/anahkiasen/isaac-mod-manager/releases/tag/%s</comment>\n Update now?",
            $version,
            $version
        );

        // Remember user choice
        $answer = $this->getOutput()->confirm($question, false);
        $cache->set('selfupdate', $answer);
        if ($answer && $callback) {
            $callback();
        }
    }
}
