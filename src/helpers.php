<?php

if (!function_exists('dd')) {
    /**
     * @param array ...$args
     */
    function dd(...$args)
    {
        dump(...$args);
        exit;
    }
}
