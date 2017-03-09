<?php

namespace Isaac\Services\Conflicts;

use Illuminate\Support\Collection;
use Isaac\Services\Mods\Mod;
use League\Flysystem\FilesystemInterface;
use Psr\SimpleCache\CacheInterface;

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
     * @var array
     */
    protected $ignored = [
        '/LICENSE.txt',
        '/metadata.xml',
    ];

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
        if ($conflicts->isEmpty()) {
            return $mods;
        }

        // Gather resolution for each conflict
        $conflicts = $conflicts->map(function (Conflict $conflict) use ($resolver) {
            return $this->resolve($conflict, $resolver);
        });

        // Get all mods excluded by the conflicts
        $excluded = $conflicts->filter->isResolved()->reduce(function (Collection $reduction, Conflict $conflict) {
            return $reduction->merge($conflict->getExcluded());
        }, new Collection());

        return $mods->reject(function (Mod $mod) use ($excluded) {
            return $excluded->contains($mod);
        });
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
                $filepath = str_replace($mod->getPath(), null, $file['path']);
                if (in_array($filepath, $this->ignored, true)) {
                    continue;
                }

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

        return $conflicts->filter->isConflict();
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
