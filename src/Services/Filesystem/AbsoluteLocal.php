<?php

namespace Isaac\Services\Filesystem;

use League\Flysystem\Adapter\Local;

/**
 * A fork of the Local adapter that grants us permissions to any absolute path
 * instead of working with relative paths.
 */
class AbsoluteLocal extends Local
{
    /**
     * AbsoluteLocal constructor.
     */
    public function __construct()
    {
        $this->permissionMap = static::$permissions;
        $this->writeFlags = LOCK_EX;
    }

    /**
     * {@inheritdoc}
     */
    public function applyPathPrefix($path)
    {
        return mb_strpos(mb_strtolower(PHP_OS), 'win') === 0 ? $path : '/'.$path;
    }
}
