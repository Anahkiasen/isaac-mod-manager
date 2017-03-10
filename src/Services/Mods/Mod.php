<?php

namespace Isaac\Services\Mods;

use Exception;
use Illuminate\Support\Arr;
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
     * @var array
     */
    protected $ignored = [
        'LICENSE.txt',
        'metadata.xml',
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->path = $attributes['path'];
    }

    /**
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
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
            $metadata = @simplexml_load_string($metadata, 'SimpleXMLElement', LIBXML_NOCDATA);
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
     * @return array
     */
    public function listFiles(): array
    {
        return array_filter($this->filesystem->listFiles($this->getPath(), true), function ($file) {
            return !in_array($file['basename'], $this->ignored, true);
        });
    }
}
