<?php

namespace Isaac\Services\Filesystem;

use League\Flysystem\Adapter\Local;

class LooseLocal extends Local
{
    /**
     * LooseLocal constructor.
     */
    public function __construct()
    {
        // Ignore security checks and stuff like that
    }
}
