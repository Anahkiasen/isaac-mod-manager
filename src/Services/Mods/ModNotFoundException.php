<?php

namespace Isaac\Services\Mods;

class ModNotFoundException extends \InvalidArgumentException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($modId)
    {
        $modId = (array) $modId;

        parent::__construct('Cannot find mod(s) with ID: '.implode(', ', $modId));
    }
}
