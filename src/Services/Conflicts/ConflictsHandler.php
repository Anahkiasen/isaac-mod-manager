<?php

namespace Isaac\Services\Conflicts;

use Illuminate\Support\Collection;
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
     * @param Collection $mods
     *
     * @return Conflict[]|Collection
     */
    public function findConflicts(Collection $mods): Collection
    {
        $paths = [];
        foreach ($mods as $mod) {
            foreach ($this->filesystem->listFiles($mod->getPath()) as $file) {
                $filepath = str_replace($mod->getPath(), null, $file['path']);
                if (in_array($filepath, $this->ignored, true)) {
                    continue;
                }

                if (!isset($paths[$filepath])) {
                    $paths[$filepath] = new Conflict();
                }

                $paths[$filepath][] = $mod;
            }
        }

        return collect($paths)->filter(function (Conflict $conflict) {
            return $conflict->count() > 1;
        });
    }
}
