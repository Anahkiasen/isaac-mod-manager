<?php

namespace Isaac\Services\Environment;

/**
 * Returns informations about the current environment IMM
 * is running in.
 */
class Environment
{
    /**
     * @return string
     */
    public static function getUsername(): string
    {
        return $_SERVER['USER'] ?? basename(getenv('HOMEPATH'));
    }

    /**
     * @return bool
     */
    public static function isMac(): bool
    {
        return PHP_OS === 'Darwin';
    }

    /**
     * @return bool
     */
    public static function isUnix(): bool
    {
        return in_array(PHP_OS, ['Linux', 'Darwin'], true);
    }
}
