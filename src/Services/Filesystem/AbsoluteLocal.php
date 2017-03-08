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
     * LooseLocal constructor.
     */
    public function __construct()
    {
        $this->permissionMap = static::$permissions;
        $this->writeFlags = LOCK_EX;
    }
}
