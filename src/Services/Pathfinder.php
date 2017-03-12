<?php

namespace Isaac\Services;

use Isaac\Services\Mods\Mod;
use League\Flysystem\Util;
use Psr\SimpleCache\CacheInterface;

/**
 * Returns various available paths.
 */
class Pathfinder
{
    /**
     * @var string
     */
    const BACKUP_PREFIX = '.pristine';

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    ////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// PLATFORM ////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $_SERVER['USER'] ?? basename(getenv('HOMEPATH'));
    }

    /**
     * @return bool
     */
    public function isMac(): bool
    {
        return PHP_OS === 'Darwin';
    }

    /**
     * @return bool
     */
    public function isUnix(): bool
    {
        return in_array(PHP_OS, ['Linux', 'Darwin'], true);
    }

    /**
     * Get the name of the game used for folders.
     *
     * @return string
     */
    public function getGameName(): string
    {
        return 'The Binding of Isaac Rebirth';
    }

    ////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////// GAME //////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * @return string
     */
    public function getGamePath(): string
    {
        if ($path = $this->cache->get('destination')) {
            return $path;
        }

        switch (PHP_OS) {
            case 'Darwin':
                return sprintf('/Users/%s/Library/Application Support/Steam/steamapps/common/%s', $this->getUsername(), $this->getGameName());

            // I know this is not correct, bear with me
            case 'Linux':
                return '/mnt/c/Program Files (x86)/Steam/steamapps/common/'.$this->getGameName();

            default:
                return 'C:/Program Files (x86)/Steam/steamapps/common/'.$this->getGameName();
        }
    }

    /**
     * @return string
     */
    public function getSavedataPath(): string
    {
        return $this->getGamePath().DS.'savedatapath.txt';
    }

    ////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// RESOURCES ///////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * @return string
     */
    public function getResourcesPath(): string
    {
        return $this->isMac() ? $this->getGamePath().'/'.$this->getGameName().'.app/Contents/Resources/resources' : $this->getGamePath().DS.'resources';
    }

    /**
     * @return string
     */
    public function getResourcesBackupPath(): string
    {
        return $this->getResourcesPath().static::BACKUP_PREFIX;
    }

    /**
     * @return string
     */
    public function getPackedPath(): string
    {
        return $this->getResourcesPath().DS.'packed';
    }

    /**
     * @return string
     */
    public function getPackedBackupPath(): string
    {
        return $this->getPackedPath().static::BACKUP_PREFIX;
    }

    /**
     * @return string
     */
    public function getMainLuaPath(): string
    {
        return $this->getResourcesPath().DS.'scripts'.DS.'main.lua';
    }

    /**
     * @return string
     */
    public function getMainLuaBackupPath(): string
    {
        return str_replace($this->getResourcesPath(), $this->getResourcesBackupPath(), $this->getMainLuaPath());
    }

    ////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////// TOOLS /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * @return string
     */
    public function getResourceExtractorPath(): string
    {
        $extension = $this->isUnix() ? '' : '.exe';

        return $this->getGamePath().DS.'tools'.DS.'ResourceExtractor'.DS.'ResourceExtractor'.$extension;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////// MODS //////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * @return string
     */
    public function getModsPath(): string
    {
        if ($path = $this->cache->get('source')) {
            return $path;
        }

        switch (PHP_OS) {
            case 'Darwin':
                return sprintf('/Users/%s/Library/Application Support/Steam/steamapps/workshop/content/250900', $this->getUsername());

            case 'Linux':
                return sprintf('/mnt/c/Users/%s/Documents/My Games/Binding of Isaac Afterbirth+ Mods', $this->getUsername());

            default:
                return sprintf('C:/Users/%s/Documents/My Games/Binding of Isaac Afterbirth+ Mods', $this->getUsername());
        }
    }

    /**
     * @param Mod    $mod
     * @param string $filepath
     *
     * @return string
     */
    public function getModeFileInResources(Mod $mod, string $filepath): string
    {
        return $this->getModFileIn($mod, $filepath, $this->getResourcesPath());
    }

    /**
     * @param Mod    $mod
     * @param string $filepath
     *
     * @return string
     */
    public function getModeFileInResourcesBackup(Mod $mod, string $filepath): string
    {
        return $this->getModFileIn($mod, $filepath, $this->getResourcesBackupPath());
    }

    /**
     * @param Mod    $mod
     * @param string $filepath
     * @param        $in
     *
     * @return string
     */
    protected function getModFileIn(Mod $mod, string $filepath, $in): string
    {
        $filepath = Util::normalizePath($filepath);
        $modResources = Util::normalizePath($mod->getPath());
        $relativePath = str_replace($modResources, null, $filepath);

        return Util::normalizePath($in.$relativePath);
    }
}
