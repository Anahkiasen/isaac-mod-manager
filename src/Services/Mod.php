<?php

namespace Isaac\Services;

use Exception;

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
            $metadata = file_get_contents($metadata);

            // Parse to array
            $metadata = @simplexml_load_string($metadata, 'SimpleXMLElement', LIBXML_NOCDATA);
            $metadata = json_decode(json_encode($metadata), true);
        } catch (Exception $exception) {
            $metadata = [];
        }

        return $key ? array_get($metadata, $key) : $metadata;
    }
}
