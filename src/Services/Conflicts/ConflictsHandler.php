<?php

namespace Isaac\Services\Conflicts;

use Illuminate\Support\Collection;
use Isaac\Services\Mods\Mod;
use League\Flysystem\FilesystemInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Finds conflicts, solves them, remembers the solution
 * for next time, and filters mods based on conflicts.
 */
class ConflictsHandler
{
    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @param CacheInterface      $cache
     * @param FilesystemInterface $filesystem
     */
    public function __construct(CacheInterface $cache, FilesystemInterface $filesystem)
    {
        $this->cache = $cache;
        $this->filesystem = $filesystem;
    }

    /**
     * @param Collection   $mods
     * @param int|callable $resolver
     *
     * @return Collection
     */
    public function findAndResolve(Collection $mods, $resolver): Collection
    {
        $conflicts = $this->findConflicts($mods);

        // Gather resolution for conflicts that require it
        if ($conflicts->isNotEmpty()) {
            $conflicts = $conflicts->map(function (Conflict $conflict) use ($resolver) {
                return $conflict->isConflict() ? $this->resolve($conflict, $resolver) : $conflict;
            });
        }

        // Bind conflict resolutions to the mods
        return $mods->map(function (Mod $mod) use ($conflicts) {
            $mod->setResolutions(new Resolutions(
                $conflicts->filter->contains($mod)
            ));

            return $mod;
        });
    }

    /**
     * @param Collection $mods
     *
     * @return Collection
     */
    public function findUnresolvedConflicts(Collection $mods): Collection
    {
        return $this->findConflicts($mods)->filter->isConflict();
    }

    /**
     * Find all conflicts in a given list of mods.
     *
     * @param Mod[]|Collection $mods
     *
     * @return Conflict[]|Collection
     */
    public function findConflicts(Collection $mods): Collection
    {
        // Gather all conflicts as a collection
        $paths = [];
        foreach ($mods as $mod) {
            foreach ($mod->listFiles() as $file) {
                $filepath = $file['relative'];

                // Append Mod to list of conflicts for this path
                $paths[$filepath] = $paths[$filepath] ?? Conflict::forPath($filepath);
                $paths[$filepath][] = $mod;
            }
        }

        // Get back past solutions to the found conflicts
        $conflicts = collect(array_values($paths))->map(function (Conflict $conflict) {
            return $conflict->resolve(
                $this->cache->get($conflict->getHash())
            );
        });

        return $conflicts;
    }

    /**
     * Resolve a given conflict.
     *
     * @param Conflict           $conflict
     * @param int|int[]|callable $resolver
     *
     * @return Conflict
     */
    public function resolve(Conflict $conflict, $resolver): Conflict
    {
        $conflict = $conflict->resolve(is_callable($resolver) ? $resolver($conflict) : $resolver);
        if ($resolution = $conflict->getResolution()) {
            $this->cache->set(
                $conflict->getHash(),
                $resolution
            );
        }

        return $conflict;
    }
}
