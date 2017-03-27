<?php

namespace Isaac;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class ApplicationTest extends TestCase
{
    public function testCanBindCommands()
    {
        $app = new Application();
        $app->doRun(new ArrayInput([]), new NullOutput());

        $this->assertNotNull($app->get('mods:install'));
    }
}