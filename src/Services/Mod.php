<?php

namespace Isaac\Services;

use Illuminate\Support\Fluent;

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
     * @param string|null $path
     *
     * @return string
     */
    public function getPath(string $path = null): string
    {
        return $path ? $this->path.DIRECTORY_SEPARATOR.$path : $this->path;
    }
}