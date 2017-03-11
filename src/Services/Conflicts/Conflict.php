<?php

namespace Isaac\Services\Conflicts;

use Illuminate\Support\Collection;
use Isaac\Services\Mods\Mod;

/**
 * A conflict for a given path between multiple mods.
 */
class Conflict extends Collection
{
    /**
     * The conflicting path.
     *
     * @var string
     */
    protected $path;

    /**
     * @var int[]
     */
    protected $resolution = [];

    /**
     * @param string $path
     * @param array  $items
     *
     * @return Conflict
     */
    public static function forPath(string $path, array $items = [])
    {
        $collection = new static($items);
        $collection->setPath($path);

        return $collection;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return bool
     */
    public function canHaveMultipleResolutions(): bool
    {
        return mb_strpos($this->path, 'main.lua') !== false;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////// SERIALIZATION /////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * Get the representation of the conflict as a hash.
     *
     * @return string
     */
    public function getHash(): string
    {
        return md5($this->path.$this->map->getId()->implode('-'));
    }

    /**
     * Get the possible choices this conflict can be solved with.
     * eg. ["Mod 1 (12345)", "Mod B (12346)"].
     *
     * @return static
     */
    public function getPossibleChoices()
    {
        return $this->map(function (Mod $mod) {
            return sprintf('%s (%s)', $mod->getName(), $mod->getId());
        });
    }

    /**
     * Get a mapping of choices to mods.
     * eg. [0 => 12345, 1 => 12346].
     *
     * @return static
     */
    public function getPossibleResolutions()
    {
        return $this->mapWithKeys(function (Mod $mod) {
            return [$mod->getName() => $mod->getId()];
        });
    }

    ////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// RESOLUTION //////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * @param int|int[]|null $resolution
     *
     * @return self
     */
    public function resolve($resolution): self
    {
        $this->resolution = $resolution === null ? [] : (array) $resolution;

        return $this;
    }

    /**
     * Get the set resolution to the conflict.
     *
     * @return int[]
     */
    public function getResolution(): array
    {
        $isResolutionValid = $this->map->getId()->intersect($this->resolution)->isNotEmpty();
        $isResolutionProvided = $this->resolution !== [];

        return $isResolutionProvided && $isResolutionValid ? $this->resolution : [];
    }

    /**
     * Get all mods were excluded in this conflict.
     *
     * @return self
     */
    public function getExcluded(): self
    {
        return $this->filter(function (Mod $mod) {
            return !in_array($mod->getId(), $this->getResolution(), true);
        });
    }

    /**
     * @return bool
     */
    public function isResolved(): bool
    {
        return $this->getResolution() !== [];
    }

    /**
     * Whether there actually is a conflict here.
     *
     * @return bool
     */
    public function isConflict(): bool
    {
        return $this->count() > 1 && !$this->isResolved();
    }
}
