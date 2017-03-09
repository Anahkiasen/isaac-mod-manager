<?php

namespace Isaac\Services\Mods;

class ModNotFoundException extends \InvalidArgumentException
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
