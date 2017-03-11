<?php

if (!function_exists('dd')) {
    function dd(...$args)
    {
        dump(...$args);
        exit;
    }
}
