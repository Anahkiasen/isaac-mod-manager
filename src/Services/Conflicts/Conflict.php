<?php

namespace Isaac\Services\Conflicts;

use Illuminate\Support\Collection;
use Isaac\Services\Mods\Mod;

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
     * Get the representation of the conflict as a hash.
     *
     * @return string
     */
    public function getHash(): string
    {
        return md5($this->path.$this->map->getId()->implode('-'));
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
