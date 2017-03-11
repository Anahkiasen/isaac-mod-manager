<?php

namespace Isaac\Services\Conflicts;

use Illuminate\Support\Collection;

/**
 * Represents predefined resolutions to one or more conflicts.
 */
class Resolutions extends Collection
{
    /**
     * Get all conflicts relevant for a given path.
     *
     * @param string $path
     *
     * @return static
     */
    public function getForPath(string $path)
    {
        return $this->filter(function (Conflict $conflict) use ($path) {
            return $conflict->getPath() === $path;
        });
    }

    /**
     * Get all mods excluded by conflicts in a given path.
     *
     * @param string $path
     *
     * @return self
     */
    public function getExcludedModsForPath(string $path): self
    {
        return $this->getForPath($path)->reduce(function (Collection $reduction, Conflict $conflict) {
            return $reduction->merge($conflict->getExcluded());
        }, new static());
    }
}
