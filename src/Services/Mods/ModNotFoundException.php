<?php

namespace Isaac\Services\Mods;

class ModNotFoundException extends \InvalidArgumentException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(int $modId)
    {
        parent::__construct('Cannot find mod with ID: '.$modId);
    }
}
