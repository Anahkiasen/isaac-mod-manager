<?php

namespace Isaac\Services\Mods;

use Exception;
use Illuminate\Support\Arr;
use Isaac\Services\Filesystem\AbsoluteLocal;
use League\Flysystem\FileNotFoundException;

/**
 * @property string path
 */
class Mod
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->path = $attributes['path'];
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
            $metadata = (new AbsoluteLocal())->applyPathPrefix($metadata);

            if (!file_exists($metadata)) {
                throw new FileNotFoundException($metadata);
            }

            // Parse to array
            $metadata = file_get_contents($metadata);
            $metadata = @simplexml_load_string($metadata, 'SimpleXMLElement', LIBXML_NOCDATA);
            $metadata = json_decode(json_encode($metadata), true);
        } catch (Exception $exception) {
            $metadata = [];
        }

        return $key ? Arr::get($metadata, $key) : $metadata;
    }
}
