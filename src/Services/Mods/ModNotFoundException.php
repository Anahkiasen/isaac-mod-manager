<?php

namespace Isaac\Services\Mods;

use InvalidArgumentException;

/**
 * Exception thrown when one or more mods cannot be found.
 */
class ModNotFoundException extends InvalidArgumentException
{
    /**
     * @param int|int[] $mods
     */
    public function __construct($mods)
    {
        $mods = (array) $mods;
        if (!$mods) {
            parent::__construct('No mods found');
        } else {
            parent::__construct('Cannot find mod(s) with ID: '.implode(', ', $mods));
        }
    }
}
