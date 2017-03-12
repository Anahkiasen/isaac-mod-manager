<?php

namespace Isaac\Services\Mods;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Isaac\Services\Conflicts\Resolutions;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;

/**
 * A class representing a single mod.
 */
class Mod
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var Resolutions
     */
    protected $resolutions;

    /**
     * @var array
     */
    protected $ignored = [
        '.DS_Store',
        'LICENSE.txt',
        'metadata.xml',
    ];

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param Resolutions $resolutions
     */
    public function setResolutions(Resolutions $resolutions)
    {
        $this->resolutions = $resolutions;
    }

    ////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// METADATA ////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * Get the human-readable name of the mod.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getMetadata('name') ?: basename($this->getPath());
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->getMetadata('id');
    }

    /**
     * Check if this mod is the same as another mod.
     *
     * @param int $modId
     *
     * @return bool
     */
    public function isMod(int $modId): bool
    {
        return $this->getId() === $modId;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isNamed(string $name)
    {
        $modName = mb_strtolower($this->getName());
        $matcher = mb_strtolower($name);

        return mb_strpos($modName, $matcher) !== false;
    }

    /**
     * Whether the mod is purely graphical or not.
     *
     * @return bool
     */
    public function isGraphical(): bool
    {
        return !$this->filesystem->has($this->getPath('main.lua')) && $this->filesystem->has($this->getPath('resources'));
    }

    /**
     * Get the path to something within the mod.
     *
     * @param string|null $path
     *
     * @return string
     */
    public function getPath(string $path = null): string
    {
        return $path ? $this->path.DIRECTORY_SEPARATOR.$path : $this->path;
    }

    /**
     * Get the metadata associated with this mod.
     *
     * @param string|null $key
     *
     * @return array|mixed
     */
    public function getMetadata(string $key = null)
    {
        // Get metadata contents
        try {
            $metadata = $this->getPath('metadata.xml');
            if (!$this->filesystem->has($metadata)) {
                throw new FileNotFoundException($metadata);
            }

            // Parse to array
            $metadata = $this->filesystem->read($metadata);
            $metadata = str_replace("\x02", null, $metadata);
            $metadata = simplexml_load_string($metadata, 'SimpleXMLElement');
            $metadata = json_decode(json_encode($metadata), true);
        } catch (Exception $exception) {
            $metadata = [];
        }

        return $key ? Arr::get($metadata, $key) : $metadata;
    }

    ////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////// FILES /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * @return Collection
     */
    public function listFiles(): Collection
    {
        // Bind relative paths to files
        $files = collect($this->filesystem->listFiles($this->getPath(), true))->map(function ($file) {
            $file['relative'] = str_replace($this->getPath(), null, $file['path']);

            return $file;
        });

        // If the mod has conflicts and we solved them,
        // exclude the paths we ignored
        if ($this->resolutions) {
            $files = $files->filter(function ($file) {
                return !$this->resolutions->getExcludedModsForPath($file['relative'])->contains($this);
            });
        }

        return $files->filter(function ($file) {
            return !in_array($file['basename'], $this->ignored, true);
        });
    }
}
