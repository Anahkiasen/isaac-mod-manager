<?php

namespace Isaac\Bus\Commands;

use Psr\SimpleCache\CacheInterface;

class Setup
{
    protected $foo;

    /**
     * Setup constructor.
     *
     * @param $foo
     */
    public function __construct($foo)
    {
        $this->foo = $foo;
    }


    public function handle(CacheInterface $cache)
    {
        dump($cache, $this->foo); exit;
    }
}