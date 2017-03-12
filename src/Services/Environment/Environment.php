<?php

namespace Isaac\Services\Environment;

/**
 * Returns informations about the current environment IMM
 * is running in.
 */
class Environment
{
    /**
     * Get the OS username of the current user.
     *
     * @return string
     */
    public static function getUsername(): string
    {
        return $_SERVER['USER'] ?? basename(getenv('HOMEPATH'));
    }

    /**
     * Whether the user is running Mac or not.
     *
     * @return bool
     */
    public static function isMac(): bool
    {
        return PHP_OS === 'Darwin';
    }

    /**
     * Whether the user is running a unix compliant OS (Linux/Mac/etc.).
     *
     * @return bool
     */
    public static function isUnix(): bool
    {
        return in_array(PHP_OS, ['Linux', 'Darwin'], true);
    }
}
